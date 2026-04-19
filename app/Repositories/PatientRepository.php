<?php

namespace App\Repositories;

use App\Models\patient;

class PatientRepository extends BaseRepository
{
    public function __construct(patient $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated patients with search
     */
    public function getPaginatedWithSearch($searchTerm = null, $limit = 10)
    {
        $query = $this->model->select([
            'id',
            'first_name',
            'last_name',
            'middle_name',
            'email',
            'gender',
            'created_at'
        ]);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('id', $searchTerm);
            });
        }

        return $query
            ->withCount('cases')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get patient with all related data
     */
    public function getWithRelations($id)
    {
        return $this->model->with([
            'cases' => function ($query) {
                $query->orderBy('opened_date', 'desc');
            },
            'cases.appointments' => function ($query) {
                $query->orderBy('appointment_date', 'desc');
            },
            'cases.appointments.visit.bills.payments',
        ])->findOrFail($id);
    }
}
