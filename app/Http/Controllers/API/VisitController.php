<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisitResource;
use App\Services\VisitService;
use Illuminate\Http\Request;

// Visit controller
class VisitController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');

        $visits = $this->visitService->getCompletedVisits($search);
        return VisitResource::collection($visits);
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
