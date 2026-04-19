<?php

namespace App\Services;

use App\Repositories\AccidentDetailsRepository;

class AccidentDetailsService
{
    protected $accidentDetailsRepository;

    public function __construct(AccidentDetailsRepository $accidentDetailsRepository)
    {
        $this->accidentDetailsRepository = $accidentDetailsRepository;
    }

    /**
     * Get accident details by ID
     */
    public function getAccidentDetails($id)
    {
        return $this->accidentDetailsRepository->findById($id);
    }

    /**
     * Create accident details
     */
    public function createAccidentDetails(array $data)
    {
        return $this->accidentDetailsRepository->create($data);
    }

    /**
     * Update accident details
     */
    public function updateAccidentDetails($id, array $data)
    {
        return $this->accidentDetailsRepository->update($id, $data);
    }

    /**
     * Delete accident details
     */
    public function deleteAccidentDetails($id)
    {
        return $this->accidentDetailsRepository->delete($id);
    }
}
