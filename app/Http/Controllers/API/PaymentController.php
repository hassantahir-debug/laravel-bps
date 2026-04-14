<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search  = request()->query('search');
        $perPage = request()->query('per_page', 10);
        $status  = request()->query('status');
        $mode    = request()->query('mode');

        $payments = Payment::select(
            'id',
            'bill_id',
            'payment_number',
            'amount_paid',
            'payment_mode',
            'payment_date',
            'payment_status',
            'check_number',
            'bank_name',
            'transaction_reference',
            'cheque_file_path',
            'notes',
            'received_by',
            'created_at'
        )
            ->with([
                'bill:id,bill_number,bill_amount,outstanding_amount,paid_amount,status,visit_id',
                'bill.visit:id,appointment_id',
                'bill.visit.appointment:id,case_id,doctor_name',
                'bill.visit.appointment.case:id,patient_id',
                'bill.visit.appointment.case.patient:id,first_name,middle_name,last_name',
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_number', 'like', "%$search%")
                        ->orWhereRelation('bill', 'bill_number', 'like', "%$search%")
                        ->orWhereRelation(
                            'bill.visit.appointment.case.patient',
                            'first_name',
                            'like',
                            "%$search%"
                        )
                        ->orWhereRelation(
                            'bill.visit.appointment.case.patient',
                            'last_name',
                            'like',
                            "%$search%"
                        );
                });
            })
            ->when($status, fn($q, $status) => $q->where('payment_status', $status))
            ->when($mode,   fn($q, $mode)   => $q->where('payment_mode', $mode))
            ->latest('payments.created_at')
            ->paginate($perPage);

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'bill_id'               => 'required|exists:bills,id',
            'amount_paid'           => 'required|numeric|min:0.01',
            'payment_mode'          => 'required|in:Cash,Check,Bank Transfer,Credit Card,Debit Card,Insurance,Online Payment',
            'payment_date'          => 'required|date',
            'payment_status'        => 'required|in:Completed,Pending,Failed,Refunded',
            'check_number'          => 'nullable|string|max:100',
            'bank_name'             => 'nullable|string|max:150',
            'transaction_reference' => 'nullable|string|max:200',
            'notes'                 => 'nullable|string',
            'cheque_file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // 2. Transaction start
        return DB::transaction(function () use ($request, $validated) {

            // 3. Bill lock karo
            $bill = Bill::lockForUpdate()->findOrFail($validated['bill_id']);

            // 4. Bill status check
            if (in_array($bill->status, ['Paid', 'Cancelled', 'Written Off'])) {
                return response()->json([
                    'message' => 'Cannot post payment to a ' . $bill->status . ' bill.'
                ], 422);
            }

            // 5. Amount check
            if ($validated['amount_paid'] > $bill->outstanding_amount) {
                return response()->json([
                    'message' => 'Payment amount exceeds outstanding balance of $' . $bill->outstanding_amount
                ], 422);
            }

            // 6. File upload handle karo
            $chequeFilePath = null;
            if ($request->hasFile('cheque_file')) {
                $file     = $request->file('cheque_file');
                $fileName = $validated['bill_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
                $chequeFilePath = $file->storeAs(
                    'payments/cheques/' . $validated['bill_id'],
                    $fileName,
                    'local'
                );
            }

            // 7. Payment number generate karo
            $paymentNumber = 'PAY-' . strtoupper(uniqid());

            // 8. Payment record banao
            $payment = Payment::create([
                'bill_id'               => $validated['bill_id'],
                'payment_number'        => $paymentNumber,
                'amount_paid'           => $validated['amount_paid'],
                'payment_mode'          => $validated['payment_mode'],
                'payment_date'          => $validated['payment_date'],
                'payment_status'        => $validated['payment_status'],
                'check_number'          => $validated['check_number'] ?? null,
                'bank_name'             => $validated['bank_name'] ?? null,
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'notes'                 => $validated['notes'] ?? null,
                'cheque_file_path'      => $chequeFilePath,
                'received_by'           => auth()->id() ?? 1,
            ]);

            // 9. Bill amounts update karo
            $bill->paid_amount        += $validated['amount_paid'];
            $bill->outstanding_amount -= $validated['amount_paid'];

            // 10. Bill status auto-update
            if ($bill->outstanding_amount <= 0) {
                $bill->outstanding_amount = 0;
                $bill->status = 'Paid';
            } else {
                $bill->status = 'Partial';
            }

            $bill->save();

            // 11. Return response
            return response()->json([
                'message' => 'Payment posted successfully.',
                'data'    => [
                    'payment_number'   => $payment->payment_number,
                    'amount_paid'      => $payment->amount_paid,
                    'payment_mode'     => $payment->payment_mode,
                    'payment_status'   => $payment->payment_status,
                    'bill_status'      => $bill->status,
                    'outstanding'      => $bill->outstanding_amount,
                    'cheque_file_path' => $chequeFilePath,
                ]
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export(Request $request)
    {
        $search  = request()->query('search');
        $status  = request()->query('status');
        $payment_mode = request()->query('payment_mode');
        $dateFrom   = request()->query('date_from');
        $dateTo    = request()->query('date_to');
        $headers = ['Payment #', 'Bill #', 'Patient', 'Amount', 'Mode', 'Date', 'Status', 'Bank', 'Notes'];

        $payments = Payment::select(
            'id',
            'bill_id',
            'payment_number',
            'amount_paid',
            'payment_mode',
            'payment_date',
            'payment_status',
            'check_number',
            'bank_name',
            'transaction_reference',
            'cheque_file_path',
            'notes',
            'received_by',
            'created_at'
        )
            ->with([
                'bill:id,bill_number,bill_amount,outstanding_amount,paid_amount,status,visit_id',
                'bill.visit:id,appointment_id',
                'bill.visit.appointment:id,case_id,doctor_name',
                'bill.visit.appointment.case:id,patient_id',
                'bill.visit.appointment.case.patient:id,first_name,middle_name,last_name',
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_number', 'like', "%$search%")
                        ->orWhereRelation('bill', 'bill_number', 'like', "%$search%")
                        ->orWhereRelation(
                            'bill.visit.appointment.case.patient',
                            'first_name',
                            'like',
                            "%$search%"
                        )
                        ->orWhereRelation(
                            'bill.visit.appointment.case.patient',
                            'last_name',
                            'like',
                            "%$search%"
                        );
                });
            })
            ->when($status, fn($q, $status) => $q->where('payment_status', $status))
            ->when($payment_mode, fn($q, $payment_mode) => $q->where('payment_mode', $payment_mode))
            ->when($dateFrom, fn($q, $dateFrom) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn($q, $dateTo)   => $q->whereDate('payment_date', '<=', $dateTo))
            ->latest('payments.created_at')
            ->get();

        $rows = $payments->map(fn($p) => [
            $p->payment_number,
            $p->bill->bill_number ?? '—',
            $p->bill?->visit?->appointment?->case?->patient?->full_name ?? '—',
            $p->amount_paid,
            $p->payment_mode,
            $p->payment_date ? substr($p->payment_date, 0, 10) : '—',
            $p->payment_status,
            $p->bank_name ?? '—',
            $p->notes ?? '—',
        ]);

        $csv = collect([$headers])
            ->merge($rows)
            ->map(fn($row) => implode(',', $row))
            ->implode("\n");

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=payments_export.csv',
        ]);
    }
}
