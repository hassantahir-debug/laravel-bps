<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');
        $perPage = request()->query('per_page', 10);


        $payments = Payment::select('id', 'visit_id', 'bill_number', 'bill_amount', 'status', 'created_at', 'due_date', 'outstanding_amount', 'paid_amount', 'bill_date',)
            ->with([
                'visit:id,appointment_id,diagnosis',
                'visit.appointment:id,case_id,appointment_date,doctor_name',
                'visit.appointment.case:id,patient_id,case_number,case_type,case_category',
                'visit.appointment.case.patient:id,first_name,gender'
            ])
            ->latest('bills.created_at')
            ->get();

        return response()->json($payments);
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
