<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a paginated listing of patients with optional search.
     */
    public function index(Request $request)
    {
        // Select ONLY the fields needed by the Angular table to reduce payload size
        $query = patient::select([
            'id', 
            'first_name', 
            'last_name', 
            'middle_name', 
            'email', 
            'gender', 
            'created_at'
        ]);

        // Search by name, email, or id
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $patients = $query
            ->withCount('cases')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('limit', 10));

        return response()->json($patients);
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'              => 'required|string|max:255',
            'last_name'               => 'required|string|max:255',
            'middle_name'             => 'nullable|string|max:255',
            'email'                   => 'required|email|unique:patients,email',
            'phone'                   => 'required|string|max:20',
            'mobile'                  => 'required|string|max:20',
            'date_of_birth'           => 'required|date',
            'gender'                  => 'required|in:Male,Female,Other',
            'address'                 => 'required|string',
            'city'                    => 'required|string|max:255',
            'state'                   => 'required|string|max:255',
            'postal_code'             => 'required|string|max:20',
            'country'                 => 'required|string|max:255',
            'emergency_contact_name'  => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
        ]);

        $patient = patient::create($validated);

        return response()->json($patient, 201);
    }

    /**
     * Display a single patient with related cases, appointments, visits, and bills.
     */
    public function show(string $id)
    {
        $patient = patient::with([
            'cases' => function ($query) {
                $query->orderBy('opened_date', 'desc');
            },
            'cases.appointments' => function ($query) {
                $query->orderBy('appointment_date', 'desc');
            },
            'cases.appointments.visit.bills.payments',
        ])->findOrFail($id);

        return response()->json($patient);
    }

    /**
     * Update the specified patient.
     */
    public function update(Request $request, string $id)
    {
        $patient = patient::findOrFail($id);

        $validated = $request->validate([
            'first_name'              => 'sometimes|string|max:255',
            'last_name'               => 'sometimes|string|max:255',
            'middle_name'             => 'nullable|string|max:255',
            'email'                   => 'sometimes|email|unique:patients,email,' . $id,
            'phone'                   => 'sometimes|string|max:20',
            'mobile'                  => 'sometimes|string|max:20',
            'date_of_birth'           => 'sometimes|date',
            'gender'                  => 'sometimes|in:Male,Female,Other',
            'address'                 => 'sometimes|string',
            'city'                    => 'sometimes|string|max:255',
            'state'                   => 'sometimes|string|max:255',
            'postal_code'             => 'sometimes|string|max:20',
            'country'                 => 'sometimes|string|max:255',
            'emergency_contact_name'  => 'sometimes|string|max:255',
            'emergency_contact_phone' => 'sometimes|string|max:20',
        ]);

        $patient->update($validated);

        return response()->json($patient);
    }

    /**
     * Soft-delete the specified patient.
     */
    public function destroy(string $id)
    {
        $patient = patient::findOrFail($id);
        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }
}

