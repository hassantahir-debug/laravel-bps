<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

// Dashboard controller
class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get poster stats
     */
    public function posterStats()
    {
        $stats = $this->dashboardService->getPosterStats();
        return response()->json($stats);
    }

    /**
     * Get biller stats
     */
    public function billerStats()
    {
        $stats = $this->dashboardService->getBillerStats();
        return response()->json($stats);
    }
}
