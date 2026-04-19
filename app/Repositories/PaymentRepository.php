<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Bill;

class PaymentRepository extends BaseRepository
{
    protected $billRepository;

    public function __construct(Payment $model, BillRepository $billRepository)
    {
        parent::__construct($model);
        $this->billRepository = $billRepository;
    }

    /**
     * Get paginated payments with search and filters
     */
    public function getPaginatedWithSearch($searchTerm = null, $status = null, $mode = null, $perPage = 10)
    {
        $query = $this->model->select(
            'id',
            'bill_id',
            'payment_number',
            'amount_paid',
            'payment_mode',
            'payment_date',
            'payment_status',
            'check_number',
            'bank_name',
            'transaction_reference',
            'cheque_file_path',
            'notes',
            'received_by',
            'created_at'
        )
            ->with([
                'bill:id,bill_number,bill_amount,outstanding_amount,paid_amount,status,visit_id',
                'bill.visit:id,appointment_id',
                'bill.visit.appointment:id,case_id,doctor_name',
                'bill.visit.appointment.case:id,patient_id',
                'bill.visit.appointment.case.patient:id,first_name,middle_name,last_name',
            ]);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('payment_number', 'like', "%$searchTerm%")
                    ->orWhereRelation('bill', 'bill_number', 'like', "%$searchTerm%")
                    ->orWhereRelation(
                        'bill.visit.appointment.case.patient',
                        'first_name',
                        'like',
                        "%$searchTerm%"
                    )
                    ->orWhereRelation(
                        'bill.visit.appointment.case.patient',
                        'last_name',
                        'like',
                        "%$searchTerm%"
                    );
            });
        }

        if ($status) {
            $query->where('payment_status', $status);
        }

        if ($mode) {
            $query->where('payment_mode', $mode);
        }

        return $query->latest('payments.created_at')->paginate($perPage);
    }

    /**
     * Check if bill can accept payment
     */
    public function canBillAcceptPayment($billId)
    {
        $bill = Bill::lockForUpdate()->findOrFail($billId);

        if (in_array($bill->status, ['Paid', 'Cancelled', 'Written Off'])) {
            return [
                'valid' => false,
                'message' => 'Cannot post payment to a ' . $bill->status . ' bill.'
            ];
        }

        return ['valid' => true, 'bill' => $bill];
    }

    /**
     * Check if payment amount is valid
     */
    public function validatePaymentAmount($billId, $amount)
    {
        $bill = Bill::find($billId);
        if ($amount > $bill->outstanding_amount) {
            return [
                'valid' => false,
                'message' => 'Payment amount exceeds outstanding balance of $' . $bill->outstanding_amount
            ];
        }

        return ['valid' => true];
    }

    /**
     * Generate unique payment number
     */
    public function generatePaymentNumber()
    {
        return 'PAY-' . strtoupper(uniqid());
    }
}
