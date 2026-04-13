<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['message' => 'File received successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $insertingDocs = document::create($request->all());
            response()->json(['message' => 'inserted Successfully', $insertingDocs], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error creating bill: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
