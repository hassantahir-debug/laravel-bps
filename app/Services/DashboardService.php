<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;

class DashboardService
{
    /**
     * Get poster stats (for poster/poster role)
     */
    public function getPosterStats()
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

        return [
            'totals' => $totals,
            'bills_status' => $billsByStatus,
            'payment_modes' => $paymentModes,
            'today' => $todayCollected,
            'this_week' => $weekCollected,
            'overdue' => $overdueBills,
        ];
    }

    /**
     * Get biller stats (for biller role)
     */
    public function getBillerStats()
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

        return [
            'totals' => $totals,
            'bills_status' => $billsByStatus,
            'overdue' => $overdue,
            'today' => 0,
            'this_week' => 0,
            'payment_modes' => [],
        ];
    }
}
