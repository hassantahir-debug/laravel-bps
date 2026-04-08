<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisitResource;
use App\Models\visits;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');

        $visits = visits::select('id', 'appointment_id', 'diagnosis')
            ->where('status', 'Completed')
            ->with([
                'appointment:id,case_id,appointment_date,appointment_time,doctor_name',
                'appointment.case:id,patient_id,case_type,case_category,is_accident',
                'appointment.case.patient:id,first_name,middle_name,last_name'
            ])
            ->when($search, function ($query, $search) {
                $query->whereRelation('appointment.case.patient', 'name', 'like', "%$search%")
                    ->orWhereRelation('appointment.case', 'case_type', 'like', "%$search%")
                    ->orWhereRelation('appointment.case', 'case_category', 'like', "%$search%")
                    ->orWhereRelation('appointment', 'doctor_name', 'like', "%$search%");
            })
            ->latest()
            ->paginate(5);
        return VisitResource::collection($visits);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
