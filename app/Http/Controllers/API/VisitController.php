<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisitResource;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');

        $visits = Visit::select('id', 'appointment_id', 'diagnosis')
            ->where('status', 'Completed')
            ->withExists('bills')
            ->with([
                'appointment:id,case_id,appointment_date,appointment_time,doctor_name',
                'appointment.case:id,patient_id,case_type,case_category,is_accident',
                'appointment.case.patient:id,first_name,middle_name,last_name',
                'bills.documents'
            ])
            ->when($search, function ($query, $search) {
                $query->whereRelation('appointment.case.patient', 'first_name', 'like', "%$search%")
                    ->orWhereRelation('appointment.case.patient', 'middle_name', 'like', "%$search%")
                    ->orWhereRelation('appointment.case.patient', 'last_name', 'like', "%$search%")
                    ->orWhereRelation('appointment.case', 'case_type', 'like', "%$search%")
                    ->orWhereRelation('appointment.case', 'case_category', 'like', "%$search%")
                    ->orWhereRelation('appointment', 'doctor_name', 'like', "%$search%");
            })
            ->latest()
            ->paginate(10);
        return VisitResource::collection($visits);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
        public function update(Request $request, string $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
