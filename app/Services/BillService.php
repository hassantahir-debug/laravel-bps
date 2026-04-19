<?php

namespace App\Services;

use App\Repositories\BillRepository;

class BillService
{
    protected $billRepository;

    public function __construct(BillRepository $billRepository)
    {
        $this->billRepository = $billRepository;
    }

    /**
     * Get paginated bills list with search and filters
     */
    public function getBills($search = null, $status = null, $perPage = 10)
    {
        return $this->billRepository->getPaginatedWithSearch($search, $status, $perPage);
    }

    /**
     * Get bill details with all relations
     */
    public function getBillDetails($id)
    {
        return $this->billRepository->getWithRelations($id);
    }

    /**
     * Calculate bill amount based on charges, insurance, adjustments, and taxes
     */
    public function calculateBillAmount($grossCharges, $insuranceCredit, $adjustments, $taxAndSurcharges)
    {
        return round($grossCharges - $insuranceCredit - $adjustments + $taxAndSurcharges, 2);
    }

    /**
     * Generate unique bill number
     */
    public function generateBillNumber()
    {
        return 'BILL-' . strtoupper(uniqid());
    }

    /**
     * Create a new bill
     */
    public function createBill(array $data)
    {
        // Calculate bill amount
        $billAmount = $this->calculateBillAmount(
            $data['grossCharges'],
            $data['insuranceCredit'],
            $data['adjustments'],
            $data['taxAndSurcharges']
        );

        // Prepare data for creation
        $billData = [
            'visit_id' => $data['visit_id'],
            'bill_number' => $this->generateBillNumber(),
            'bill_amount' => $billAmount,
            'paid_amount' => 0.00,
            'procedure_codes' => $data['procedureCodes'],
            'charges' => $data['grossCharges'],
            'insurance_coverage' => $data['insuranceCredit'],
            'tax_amount' => $data['taxAndSurcharges'],
            'outstanding_amount' => $billAmount,
            'status' => 'Pending',
            'bill_date' => now(),
            'due_date' => $data['dueDate'],
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $this->billRepository->create($billData);
    }

    /**
     * Update an existing bill
     */
    public function updateBill($id, array $data)
    {
        $bill = $this->billRepository->findById($id);

        // Calculate new bill amount
        $billAmount = $this->calculateBillAmount(
            $data['grossCharges'],
            $data['insuranceCredit'],
            $data['adjustments'],
            $data['taxAndSurcharges']
        );

        // Prepare update data
        $updateData = [
            'visit_id' => $data['visit_id'],
            'bill_amount' => $billAmount,
            'procedure_codes' => $data['procedureCodes'],
            'charges' => $data['grossCharges'],
            'insurance_coverage' => $data['insuranceCredit'],
            'tax_amount' => $data['taxAndSurcharges'],
            'outstanding_amount' => round($billAmount - $bill->paid_amount, 2),
            'due_date' => $data['dueDate'],
            'notes' => $data['notes'] ?? null,
            'updated_at' => now(),
        ];

        return $this->billRepository->update($id, $updateData);
    }

    /**
     * Delete bill (with validation)
     */
    public function deleteBill($id)
    {
        $bill = $this->billRepository->findById($id);

        if ($bill->paid_amount > 0) {
            throw new \Exception('Integrity Violation: Cannot delete a bill with existing payments.');
        }

        return $this->billRepository->delete($id);
    }
}
