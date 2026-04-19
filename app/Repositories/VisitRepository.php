<?php

namespace App\Repositories;

use App\Models\Visit;

class VisitRepository extends BaseRepository
{
    public function __construct(Visit $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated completed visits with search
     */
    public function getPaginatedCompletedWithSearch($searchTerm = null, $perPage = 10)
    {
        $query = $this->model
            ->select('id', 'appointment_id', 'diagnosis')
            ->where('status', 'Completed')
            ->withExists('bills')
            ->with([
                'appointment:id,case_id,appointment_date,appointment_time,doctor_name',
                'appointment.case:id,patient_id,case_type,case_category,is_accident',
                'appointment.case.patient:id,first_name,middle_name,last_name',
                'bills.documents'
            ]);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRelation('appointment.case.patient', 'first_name', 'like', "%$searchTerm%")
                    ->orWhereRelation('appointment.case.patient', 'middle_name', 'like', "%$searchTerm%")
                    ->orWhereRelation('appointment.case.patient', 'last_name', 'like', "%$searchTerm%")
                    ->orWhereRelation('appointment.case', 'case_type', 'like', "%$searchTerm%")
                    ->orWhereRelation('appointment.case', 'case_category', 'like', "%$searchTerm%")
                    ->orWhereRelation('appointment', 'doctor_name', 'like', "%$searchTerm%");
            });
        }

        return $query->latest()->paginate($perPage);
    }
}
