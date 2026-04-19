<?php

namespace App\Services;

use App\Repositories\InsuranceRepository;

class InsuranceService
{
    protected $insuranceRepository;

    public function __construct(InsuranceRepository $insuranceRepository)
    {
        $this->insuranceRepository = $insuranceRepository;
    }

    /**
     * Get all insurance records
     */
    public function getAllInsurance()
    {
        return $this->insuranceRepository->all();
    }

    /**
     * Create insurance record
     */
    public function createInsurance(array $data)
    {
        return $this->insuranceRepository->create($data);
    }

    /**
     * Get insurance by ID
     */
    public function getInsuranceById($id)
    {
        return $this->insuranceRepository->findById($id);
    }

    /**
     * Update insurance record
     */
    public function updateInsurance($id, array $data)
    {
        return $this->insuranceRepository->update($id, $data);
    }

    /**
     * Delete insurance record
     */
    public function deleteInsurance($id)
    {
        return $this->insuranceRepository->delete($id);
    }
}
