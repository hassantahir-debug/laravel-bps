<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');
        $bills = Bill::select('id', 'visit_id', 'bill_number', 'bill_amount', 'status', 'created_at', 'due_date', 'outstanding_amount', 'paid_amount')
            ->with([
                'visit:id,appointment_id,diagnosis',
                'visit.appointment:id,case_id,appointment_date,doctor_name',
                'visit.appointment.case:id,patient_id,case_type,case_category',
                'visit.appointment.case.patient:id,first_name,middle_name,last_name'
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRelation('visit.appointment.case.patient', 'first_name', 'like', "%$search%")
                        ->orWhereRelation('visit.appointment.case', 'case_type', 'like', "%$search%")
                        ->orWhereRelation('visit.appointment', 'doctor_name', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(5);
        return BillResource::collection($bills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
}
