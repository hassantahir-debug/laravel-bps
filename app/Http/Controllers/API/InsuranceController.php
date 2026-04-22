<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\InsuranceService;
use Illuminate\Http\Request;

// Insurance controller
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:insurances,email',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'city' => 'nullable|string|max:100',
            'contactPerson' => 'nullable|string|max:255',
            'contractExpiry' => 'nullable|date',
            'status' => 'required|in:ACTIVE,PENDING,INACTIVE',
        ]);

        try {
            $insurance = $this->insuranceService->createInsurance($validated);
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
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:insurances,email,' . $id,
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'city' => 'nullable|string|max:100',
            'contactPerson' => 'nullable|string|max:255',
            'contractExpiry' => 'nullable|date',
            'status' => 'sometimes|required|in:ACTIVE,PENDING,INACTIVE',
        ]);

        try {
            $insurance = $this->insuranceService->updateInsurance($id, $validated);
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
