<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $documents = $this->documentService->getAllDocuments();
            return response()->json(['message' => 'Documents fetched successfully', 'data' => $documents], 200);
        } catch (\Throwable $th) {
           return response()->json(['message' => 'Error fetching documents: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $document = $this->documentService->createDocument($request->all());
            return response()->json(['message' => 'inserted Successfully', $document], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error creating document: ' . $th->getMessage()], 500);
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
