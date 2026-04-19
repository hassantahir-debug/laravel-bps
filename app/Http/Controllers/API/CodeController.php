<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ProcedureCodeService;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    protected $procedureCodeService;

    public function __construct(ProcedureCodeService $procedureCodeService)
    {
        $this->procedureCodeService = $procedureCodeService;
    }

    /**
     * Get all procedure codes
     */
    public function index()
    {
        return $this->procedureCodeService->getAllCodes();
    }

    /**
     * Store a newly created procedure code
     */
    public function store(Request $request)
    {
        try {
            $code = $this->procedureCodeService->createCode($request->all());
            return response()->json(["message" => "created successfully", "procedureCode" => $code], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    /**
     * Update procedure code
     */
    public function update(Request $request, string $id)
    {
        try {
            $code = $this->procedureCodeService->updateCode($id, $request->all());
            return response()->json($code, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->procedureCodeService->deleteCode($id);
            return response()->json(['message' => 'Procedure code deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
