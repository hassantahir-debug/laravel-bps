<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Get paginated payments with search and filters
     */
    public function getPayments($search = null, $status = null, $mode = null, $perPage = 10)
    {
        return $this->paymentRepository->getPaginatedWithSearch($search, $status, $mode, $perPage);
    }

    /**
     * Process a payment with transaction
     */
    public function processPayment(array $validatedData, $chequeFilePath = null)
    {
        return DB::transaction(function () use ($validatedData, $chequeFilePath) {
            // 1. Check if bill can accept payment
            $billCheck = $this->paymentRepository->canBillAcceptPayment($validatedData['bill_id']);
            if (!$billCheck['valid']) {
                throw new \Exception($billCheck['message']);
            }

            $bill = $billCheck['bill'];

            // 2. Validate payment amount
            $amountCheck = $this->paymentRepository->validatePaymentAmount(
                $validatedData['bill_id'],
                $validatedData['amount_paid']
            );
            if (!$amountCheck['valid']) {
                throw new \Exception($amountCheck['message']);
            }

            // 3. Generate payment number
            $paymentNumber = $this->paymentRepository->generatePaymentNumber();

            // 4. Create payment record
            $paymentData = [
                'bill_id' => $validatedData['bill_id'],
                'payment_number' => $paymentNumber,
                'amount_paid' => $validatedData['amount_paid'],
                'payment_mode' => $validatedData['payment_mode'],
                'payment_date' => $validatedData['payment_date'],
                'payment_status' => $validatedData['payment_status'],
                'check_number' => $validatedData['check_number'] ?? null,
                'bank_name' => $validatedData['bank_name'] ?? null,
                'transaction_reference' => $validatedData['transaction_reference'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'cheque_file_path' => $chequeFilePath,
                'received_by' => request()->attributes->get('authenticated_user')->id ?? 1,
            ];

            $payment = $this->paymentRepository->create($paymentData);

            // 5. Update bill amounts
            $bill->paid_amount += $validatedData['amount_paid'];
            $bill->outstanding_amount -= $validatedData['amount_paid'];

            // 6. Auto-update bill status
            if ($bill->outstanding_amount <= 0) {
                $bill->outstanding_amount = 0;
                $bill->status = 'Paid';
            } else {
                $bill->status = 'Partial';
            }

            $bill->save();

            return $payment->load('bill.visit.appointment.case.patient');
        });
    }

    /**
     * Handle file upload for cheques
     */
    public function uploadChequeFile($file, $billId)
    {
        if (!$file) {
            return null;
        }

        $fileName = $billId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs(
            'payments/cheques/' . $billId,
            $fileName,
            'local'
        );
    }

    /**
     * Update payment
     */
    public function updatePayment($id, array $data)
    {
        return $this->paymentRepository->update($id, $data);
    }

    /**
     * Delete payment
     */
    public function deletePayment($id)
    {
        return $this->paymentRepository->delete($id);
    }
}
