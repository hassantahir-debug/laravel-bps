<?php

namespace App\Services;

use App\Repositories\VisitRepository;

class VisitService
{
    protected $visitRepository;

    public function __construct(VisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    /**
     * Get paginated completed visits with search
     */
    public function getCompletedVisits($search = null, $perPage = 10)
    {
        return $this->visitRepository->getPaginatedCompletedWithSearch($search, $perPage);
    }

    /**
     * Get visit by ID
     */
    public function getVisitById($id)
    {
        return $this->visitRepository->findById($id);
    }
}
