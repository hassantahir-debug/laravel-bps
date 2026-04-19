<?php

namespace App\Http\Middleware\Validation;

use Closure;
use Illuminate\Http\Request;

class ValidateDocument
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'bill_id' => 'nullable|exists:bills,id',
                'file_name' => 'required|string|max:255',
                'file_path' => 'required|string',
                'document_type' => 'required|string|max:100',
            ]);
        }

        return $next($request);
    }
}
