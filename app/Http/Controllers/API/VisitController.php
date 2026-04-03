<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisitResource;
use App\Models\visits;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = visits::select('id', 'appointment_id', 'diagnosis')
            ->with([
                'appointment:id,case_id,appointment_date,appointment_time,doctor_name',
                'appointment.case:id,patient_id,case_type,case_category,is_accident',
                'appointment.case.patient:id,name'
            ])
            ->latest()
            ->paginate(15);
        return VisitResource::collection($visits);
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
