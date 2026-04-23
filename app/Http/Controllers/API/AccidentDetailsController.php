<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AccidentDetailsService;
use Illuminate\Http\Request;

// Accident controller
class AccidentDetailsController extends Controller
{
    protected $accidentDetailsService;

    public function __construct(AccidentDetailsService $accidentDetailsService)
    {
        $this->accidentDetailsService = $accidentDetailsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(["message" => "addd"], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $accidentDetails = $this->accidentDetailsService->createAccidentDetails($request->all());
            return response()->json($accidentDetails, 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->accidentDetailsService->getAccidentDetails($id);
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $accidentDetails = $this->accidentDetailsService->updateAccidentDetails($id, $request->all());
            return response()->json($accidentDetails, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->accidentDetailsService->deleteAccidentDetails($id);
            return response()->json(['message' => 'Accident details deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
