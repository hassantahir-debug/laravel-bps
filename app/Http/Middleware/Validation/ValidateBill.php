<?php

namespace App\Http\Middleware\Validation;

use Closure;
use Illuminate\Http\Request;

class ValidateBill
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $request->validate([
                'visit_id' => 'required|integer|exists:visits,id',
                'grossCharges' => 'required|numeric|min:0',
                'insuranceCredit' => 'required|numeric|min:0',
                'adjustments' => 'required|numeric|min:0',
                'taxAndSurcharges' => 'required|numeric|min:0',
                'procedureCodes' => 'required|array',
                'procedureCodes.*.id' => 'required|integer',
                'procedureCodes.*.code' => 'required|string',
                'procedureCodes.*.description' => 'required|string',
                'procedureCodes.*.price' => 'required|numeric|min:0',
                'dueDate' => 'required|date',
                'notes' => 'nullable|string',
            ]);
        }

        return $next($request);
    }
}
