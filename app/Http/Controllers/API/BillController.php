<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');
        $perPage = request()->query('per_page', 10);

        $bills = Bill::select(
            'id',
            'visit_id',
            'bill_number',
            'bill_amount',
            'status',
            'created_at',
            'due_date',
            'outstanding_amount',
            'paid_amount',
            'charges',
            'bill_date',
            'discount_amount',
            'generated_document_path',
            'insurance_coverage',
            'notes',
            'procedure_codes',
            'tax_amount',
        )
            ->with([
                'visit:id,appointment_id,diagnosis',
                'visit.appointment:id,case_id,appointment_date,doctor_name',
                'visit.appointment.case:id,patient_id,case_type,case_category',
                'visit.appointment.case.patient:id,first_name,middle_name,last_name'
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('bill_number', 'like', "%$search%")
                        ->orWhereRelation('visit.appointment.case.patient', 'first_name', 'like', "%$search%")
                        ->orWhereRelation('visit.appointment.case.patient', 'last_name', 'like', "%$search%")
                        ->orWhereRelation('visit.appointment', 'doctor_name', 'like', "%$search%");
                });
            })
            ->when(request()->query('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('bills.created_at')
            ->paginate($perPage);

        return BillResource::collection($bills);
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visit_id' => 'required|integer|exists:visits,id',
            'grossCharges' => 'required|numeric|min:0',
            'insuranceCredit' => 'required|numeric|min:0',
            'adjustments' => 'required|numeric|min:0',
            'taxAndSurcharges' => 'required|numeric|min:0',
            'procedureCodes' => 'required|array',
            'procedureCodes.*.id' => 'required|integer',
            'procedureCodes.*.code' => 'required|string',
            'procedureCodes.*.description' => 'required|string',
            'procedureCodes.*.price' => 'required|numeric|min:0',
            'dueDate' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $billAmount = round($validated['grossCharges'] - $validated['insuranceCredit'] - $validated['adjustments'] + $validated['taxAndSurcharges'], 2);
            $inserted = Bill::create([
                'visit_id' => $validated['visit_id'],
                'bill_number' => 'BILL-' . strtoupper(uniqid()),
                'bill_amount' => $billAmount,
                'paid_amount' => 0.00,
                'procedure_codes' => $validated['procedureCodes'],
                'charges' => $validated['grossCharges'],
                'insurance_coverage' => $validated['insuranceCredit'],
                'tax_amount' => $validated['taxAndSurcharges'],
                'outstanding_amount' => round($billAmount - 0.00, 2),
                'status' => 'Pending',
                'bill_date' => now(),
                'due_date' => $validated['dueDate'],
                'notes' => $validated['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json($inserted, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error creating bill: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new BillResource(
            Bill::select([
                'id',
                'visit_id',
                'bill_number',
                'bill_amount',
                'status',
                'created_at',
                'due_date',
                'outstanding_amount',
                'paid_amount',
                'charges',
                'bill_date',
                'discount_amount',
                'generated_document_path',
                'insurance_coverage',
                'notes',
                'procedure_codes',
                'tax_amount',
            ])
                ->with([
                    'visit:id,appointment_id,diagnosis',
                    'visit.appointment:id,case_id,appointment_date,doctor_name',
                    'visit.appointment.case:id,patient_id,case_type',
                    'visit.appointment.case.patient:id,first_name,middle_name,last_name',
                    'payments' => function ($query) {
                        $query->select('id', 'bill_id', 'amount_paid', 'payment_mode', 'payment_date', 'payment_status')
                            ->orderBy('payment_date', 'desc');
                    },
                    'documents:id,bill_id,file_name,file_path,document_type'
                ])
                ->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
            $validated = $request->validate([
                'visit_id' => 'required|integer|exists:visits,id',
                'grossCharges' => 'required|numeric|min:0',
                'insuranceCredit' => 'required|numeric|min:0',
                'adjustments' => 'required|numeric|min:0',
                'taxAndSurcharges' => 'required|numeric|min:0',
                'procedureCodes' => 'required|array',
                'procedureCodes.*.id' => 'required|integer',
                'procedureCodes.*.code' => 'required|string',
                'procedureCodes.*.description' => 'required|string',
                'procedureCodes.*.price' => 'required|numeric|min:0',
                'dueDate' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            try {
                $billAmount = round($validated['grossCharges'] - $validated['insuranceCredit'] - $validated['adjustments'] + $validated['taxAndSurcharges'], 2);
                $bill = Bill::findOrFail($id);
                $bill->update([
                    'visit_id' => $validated['visit_id'],
                    'bill_amount' => $billAmount,
                    'procedure_codes' => $validated['procedureCodes'],
                    'charges' => $validated['grossCharges'],
                    'insurance_coverage' => $validated['insuranceCredit'],
                    'tax_amount' => $validated['taxAndSurcharges'],
                    'outstanding_amount' => round($billAmount - $bill->paid_amount, 2),
                    'due_date' => $validated['dueDate'],
                    'notes' => $validated['notes'],
                    'updated_at' => now(),
                ]);

                return response()->json($bill, 200);
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Error updating bill: ' . $th->getMessage()], 500);
            }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bill = Bill::findOrFail($id);

        if ($bill->paid_amount > 0) {
            return response()->json([
                'message' => 'Integrity Violation: Cannot delete a bill with existing payments.'
            ], 422);
        }

        if (!in_array($bill->status, ['Pending', 'Cancelled'])) {
            return response()->json([
                'message' => 'Status Protection: Only pending or cancelled bills can be removed.'
            ], 403);
        }

        $bill->delete();

        return response()->json([
            'message' => 'Bill removed successfully.'
        ], 200);
    }
}
