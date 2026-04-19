<?php

namespace App\Services;

use App\Repositories\PatientRepository;

class PatientService
{
    protected $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    /**
     * Get paginated patients list
     */
    public function getPatients($search = null, $limit = 10)
    {
        return $this->patientRepository->getPaginatedWithSearch($search, $limit);
    }

    /**
     * Create a new patient
     */
    public function createPatient(array $data)
    {
        return $this->patientRepository->create($data);
    }

    /**
     * Get single patient with all relations
     */
    public function getPatientDetails($id)
    {
        return $this->patientRepository->getWithRelations($id);
    }

    /**
     * Update patient information
     */
    public function updatePatient($id, array $data)
    {
        return $this->patientRepository->update($id, $data);
    }

    /**
     * Delete patient (soft delete)
     */
    public function deletePatient($id)
    {
        return $this->patientRepository->delete($id);
    }
}
