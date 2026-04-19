<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Display a paginated listing of patients with optional search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $limit = $request->input('limit', 10);

        $patients = $this->patientService->getPatients($search, $limit);

        return response()->json($patients);
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request)
    {
        $patient = $this->patientService->createPatient($request->all());

        return response()->json($patient, 201);
    }

    /**
     * Display a single patient with related cases, appointments, visits, and bills.
     */
    public function show(string $id)
    {
        $patient = $this->patientService->getPatientDetails($id);

        return response()->json($patient);
    }

    /**
     * Update the specified patient.
     */
    public function update(Request $request, string $id)
    {
        $patient = $this->patientService->updatePatient($id, $request->all());

        return response()->json($patient);
    }

    /**
     * Soft-delete the specified patient.
     */
    public function destroy(string $id)
    {
        $this->patientService->deletePatient($id);

        return response()->json(['message' => 'Patient deleted successfully']);
    }
}

