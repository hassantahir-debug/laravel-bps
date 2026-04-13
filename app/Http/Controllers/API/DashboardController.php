<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function posterStats()
    {
        // Bills by status counts + amounts
        $billsByStatus = Bill::selectRaw('
        status,
        COUNT(*) as count,
        SUM(bill_amount) as total_amount,
        SUM(outstanding_amount) as total_outstanding,
        SUM(paid_amount) as total_paid
    ')->groupBy('status')->get();

        // Overall totals
        $totals = Bill::selectRaw('
        COUNT(*) as total_bills,
        SUM(bill_amount) as total_amount,
        SUM(paid_amount) as total_collected,
        SUM(outstanding_amount) as total_outstanding
    ')->first();

        // Payment modes breakdown
        $paymentModes = Payment::selectRaw('
        payment_mode,
        COUNT(*) as count,
        SUM(amount_paid) as total
    ')->groupBy('payment_mode')->get();

        // Today collections
        $todayCollected = Payment::whereDate('payment_date', today())
            ->sum('amount_paid');

        // This week
        $weekCollected = Payment::whereBetween('payment_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->sum('amount_paid');

        // Overdue bills
        $overdueBills = Bill::whereDate('due_date', '<', today())
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->count();

        return response()->json([
            'totals'        => $totals,
            'bills_status'  => $billsByStatus,
            'payment_modes' => $paymentModes,
            'today'         => $todayCollected,
            'this_week'     => $weekCollected,
            'overdue'       => $overdueBills,
        ]);
    }

    public function billerStats()
    {
        $totals = Bill::selectRaw('
        COUNT(*) as total_bills,
        SUM(bill_amount) as total_amount,
        SUM(paid_amount) as total_collected,
        SUM(outstanding_amount) as total_outstanding
    ')->first();

        $billsByStatus = Bill::selectRaw('
        status, COUNT(*) as count,
        SUM(bill_amount) as total_amount
    ')->groupBy('status')->get();

        $overdue = Bill::whereDate('due_date', '<', today())
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->count();

        return response()->json([
            'totals'       => $totals,
            'bills_status' => $billsByStatus,
            'overdue'      => $overdue,
            'today'        => 0,
            'this_week'    => 0,
            'payment_modes' => [],
        ]);
    }
}
