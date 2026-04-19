<?php

namespace App\Http\Middleware\Validation;

use Closure;
use Illuminate\Http\Request;

class ValidatePayment
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'bill_id' => 'required|exists:bills,id',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_mode' => 'required|in:Cash,Check,Bank Transfer,Credit Card,Debit Card,Insurance,Online Payment',
                'payment_date' => 'required|date',
                'payment_status' => 'required|in:Completed,Pending,Failed,Refunded',
                'check_number' => 'nullable|string|max:100',
                'bank_name' => 'nullable|string|max:150',
                'transaction_reference' => 'nullable|string|max:200',
                'notes' => 'nullable|string',
                'cheque_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);
        }

        return $next($request);
    }
}
