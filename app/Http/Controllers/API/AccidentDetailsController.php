<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\accidentDetails;
use App\Http\Requests\StoreaccidentDetailsRequest;
use App\Http\Requests\UpdateaccidentDetailsRequest;

class AccidentDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(["message" => "addd"], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreaccidentDetailsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = accidentDetails::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(accidentDetails $accidentDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateaccidentDetailsRequest $request, accidentDetails $accidentDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(accidentDetails $accidentDetails)
    {
        //
    }
}
