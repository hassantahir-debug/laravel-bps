<?php

namespace App\Http\Middleware\Validation;

use Closure;
use Illuminate\Http\Request;

class ValidateProcedureCode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $request->validate([
                'code' => 'required|integer',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0|decimal:0,2'
            ]);
        }

        return $next($request);
    }
}
