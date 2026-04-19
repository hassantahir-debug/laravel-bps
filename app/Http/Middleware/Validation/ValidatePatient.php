<?php

namespace App\Http\Middleware\Validation;

use Closure;
use Illuminate\Http\Request;

class ValidatePatient
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:patients,email',
                'phone' => 'required|string|max:20',
                'mobile' => 'required|string|max:20',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:Male,Female,Other',
                'address' => 'required|string',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:255',
                'emergency_contact_name' => 'required|string|max:255',
                'emergency_contact_phone' => 'required|string|max:20',
            ]);
        }

        if ($request->isMethod('put') || $request->isMethod('patch')) {
            $patientId = $request->route('patient');
            $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'sometimes|email|unique:patients,email,' . $patientId,
                'phone' => 'sometimes|string|max:20',
                'mobile' => 'sometimes|string|max:20',
                'date_of_birth' => 'sometimes|date',
                'gender' => 'sometimes|in:Male,Female,Other',
                'address' => 'sometimes|string',
                'city' => 'sometimes|string|max:255',
                'state' => 'sometimes|string|max:255',
                'postal_code' => 'sometimes|string|max:20',
                'country' => 'sometimes|string|max:255',
                'emergency_contact_name' => 'sometimes|string|max:255',
                'emergency_contact_phone' => 'sometimes|string|max:20',
            ]);
        }

        return $next($request);
    }
}
