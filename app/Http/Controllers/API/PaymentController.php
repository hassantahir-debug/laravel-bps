<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');
        $perPage = request()->query('per_page', 10);
        $status = request()->query('status');
        $mode = request()->query('mode');

        $payments = $this->paymentService->getPayments($search, $status, $mode, $perPage);

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Handle file upload
            $chequeFilePath = null;
            if ($request->hasFile('cheque_file')) {
                $chequeFilePath = $this->paymentService->uploadChequeFile(
                    $request->file('cheque_file'),
                    $request->input('bill_id')
                );
            }

            // Process payment
            $payment = $this->paymentService->processPayment(
                $request->all(),
                $chequeFilePath
            );

            return response()->json([
                'message' => 'Payment posted successfully.',
                'data' => $payment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
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

    /**
     * Export payments as CSV
     */
    public function export(Request $request)
    {
        $search = request()->query('search');
        $status = request()->query('status');
        $mode = request()->query('mode');
        $dateFrom = request()->query('date_from');
        $dateTo = request()->query('date_to');
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
            ->when($mode, fn($q, $mode) => $q->where('payment_mode', $mode))
            ->when($dateFrom, fn($q, $dateFrom) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo, fn($q, $dateTo) => $q->whereDate('payment_date', '<=', $dateTo))
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
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=payments_export.csv',
        ]);
    }
}
