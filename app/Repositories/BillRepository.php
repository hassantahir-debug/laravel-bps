<?php

namespace App\Repositories;

use App\Models\Bill;

class BillRepository extends BaseRepository
{
    public function __construct(Bill $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated bills with search and filters
     */
    public function getPaginatedWithSearch($searchTerm = null, $status = null, $perPage = 10)
    {
        $query = $this->model->select([
            'id',
            'visit_id',
            'bill_number',
            'bill_amount',
            'status',
            'created_at',
            'due_date',
            'outstanding_amount',
            'paid_amount',
            'charges',
            'bill_date',
            'discount_amount',
            'generated_document_path',
            'insurance_coverage',
            'notes',
            'procedure_codes',
            'tax_amount',
        ])
            ->with([
                'visit:id,appointment_id,diagnosis',
                'visit.appointment:id,case_id,appointment_date,doctor_name',
                'visit.appointment.case:id,patient_id,case_type,case_category',
                'visit.appointment.case.patient:id,first_name,middle_name,last_name'
            ]);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('bill_number', 'like', "%$searchTerm%")
                    ->orWhereRelation('visit.appointment.case.patient', 'first_name', 'like', "%$searchTerm%")
                    ->orWhereRelation('visit.appointment.case.patient', 'last_name', 'like', "%$searchTerm%")
                    ->orWhereRelation('visit.appointment', 'doctor_name', 'like', "%$searchTerm%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest('bills.created_at')->paginate($perPage);
    }

    /**
     * Get bill with all relations for detailed view
     */
    public function getWithRelations($id)
    {
        return $this->model->select([
            'id',
            'visit_id',
            'bill_number',
            'bill_amount',
            'status',
            'created_at',
            'due_date',
            'outstanding_amount',
            'paid_amount',
            'charges',
            'bill_date',
            'discount_amount',
            'generated_document_path',
            'insurance_coverage',
            'notes',
            'procedure_codes',
            'tax_amount',
        ])
            ->with([
                'visit:id,appointment_id,diagnosis',
                'visit.appointment:id,case_id,appointment_date,doctor_name',
                'visit.appointment.case:id,patient_id,case_type',
                'visit.appointment.case.patient:id,first_name,middle_name,last_name',
                'payments' => function ($query) {
                    $query->select('id', 'bill_id', 'amount_paid', 'payment_mode', 'payment_date', 'payment_status')
                        ->orderBy('payment_date', 'desc');
                },
                'documents:id,bill_id,file_name,file_path,document_type'
            ])
            ->findOrFail($id);
    }
}
