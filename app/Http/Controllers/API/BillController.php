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
        Bill::insert([
            'visit_id' => $request->visit_id,
            'bill_number' => $request->bill_number,
            'bill_amount' => $request->bill_amount,
            'paid_amount' => 0.00,
            'outstanding_amount' => $request->bill_amount,
            'status' => 'Pending',
            'due_date' => $request->due_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json($request->all(), 200);
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
                    'payments' 
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
            'new_payment' => 'required|numeric|min:0.01',
            'notes'       => 'sometimes|string|nullable'
        ]);

        return DB::transaction(function () use ($id, $validated) {
            $bill = Bill::lockForUpdate()->findOrFail($id);

            if ($bill->status === 'Cancelled') {
                return response()->json(['message' => 'Cannot post payment to a cancelled bill'], 422);
            }

            if ($validated['new_payment'] > $bill->outstanding_amount) {
                return response()->json(['message' => 'Payment exceeds outstanding balance'], 422);
            }

            $bill->paid_amount += $validated['new_payment'];
            $bill->outstanding_amount -= $validated['new_payment'];

            $bill->status = ($bill->outstanding_amount <= 0) ? 'Paid' : 'Partial';

            $bill->save();

            return new BillResource($bill->fresh());
        });
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
