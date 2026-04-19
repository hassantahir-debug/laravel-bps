<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\InsuranceService;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    protected $insuranceService;

    public function __construct(InsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $insurances = $this->insuranceService->getAllInsurance();
            return response()->json($insurances, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $insurance = $this->insuranceService->createInsurance($request->all());
            return response()->json($insurance, 201);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $insurance = $this->insuranceService->updateInsurance($id, $request->all());
            return response()->json($insurance, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->insuranceService->deleteInsurance($id);
            return response()->json(null, 204);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}
