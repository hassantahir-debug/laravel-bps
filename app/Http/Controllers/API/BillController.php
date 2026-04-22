<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Services\BillService;
use Illuminate\Http\Request;

// Bill controller
class BillController extends Controller
{
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');
        $status = request()->query('status');
        $perPage = request()->query('per_page', 10);

        $bills = $this->billService->getBills($search, $status, $perPage);

        return BillResource::collection($bills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visit_id' => 'required|integer|exists:visits,id',
            'grossCharges' => 'required|numeric|min:0',
            'insuranceCredit' => 'required|numeric|min:0',
            'adjustments' => 'nullable|numeric|min:0',
            'taxAndSurcharges' => 'required|numeric|min:0',
            'procedureCodes' => 'required|array',
            'procedureCodes.*.id' => 'required|integer',
            'dueDate' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Provide default 0 for adjustments if null
            $validated['adjustments'] = $validated['adjustments'] ?? 0;
            $bill = $this->billService->createBill($validated);
            return response()->json($bill, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error creating bill: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bill = $this->billService->getBillDetails($id);
        return new BillResource($bill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'visit_id' => 'required|integer|exists:visits,id',
            'grossCharges' => 'required|numeric|min:0',
            'insuranceCredit' => 'required|numeric|min:0',
            'adjustments' => 'nullable|numeric|min:0',
            'taxAndSurcharges' => 'required|numeric|min:0',
            'procedureCodes' => 'required|array',
            'procedureCodes.*.id' => 'required|integer',
            'dueDate' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $validated['adjustments'] = $validated['adjustments'] ?? 0;
            $bill = $this->billService->updateBill($id, $validated);
            return response()->json($bill, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error updating bill: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->billService->deleteBill($id);
            return response()->json([
                'message' => 'Bill removed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
