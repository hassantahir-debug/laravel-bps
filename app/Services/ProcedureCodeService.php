<?php

namespace App\Services;

use App\Repositories\ProcedureCodeRepository;

// Procedure service
class ProcedureCodeService
{
    protected $procedureCodeRepository;

    public function __construct(ProcedureCodeRepository $procedureCodeRepository)
    {
        $this->procedureCodeRepository = $procedureCodeRepository;
    }

    /**
     * Get all procedure codes
     */
    public function getAllCodes()
    {
        return $this->procedureCodeRepository->all();
    }

    /**
     * Create procedure code
     */
    public function createCode(array $data)
    {
        return $this->procedureCodeRepository->create($data);
    }

    /**
     * Get procedure code by ID
     */
    public function getCodeById($id)
    {
        return $this->procedureCodeRepository->findById($id);
    }

    /**
     * Update procedure code
     */
    public function updateCode($id, array $data)
    {
        return $this->procedureCodeRepository->update($id, $data);
    }

    /**
     * Delete procedure code
     */
    public function deleteCode($id)
    {
        return $this->procedureCodeRepository->delete($id);
    }
}
