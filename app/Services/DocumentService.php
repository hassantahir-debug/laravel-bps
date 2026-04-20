<?php

namespace App\Services;

use App\Repositories\DocumentRepository;

class DocumentService
{
    protected $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Create a document
     */
    public function createDocument(array $data)
    {
        return $this->documentRepository->createDocument($data);
    }

    /**
     * Get all documents
     */
    public function getAllDocuments($page, $search)
    {
        return $this->documentRepository->getPaginatedResults($page, $search);
    }
    public function delete($id)
    {
        return $this->documentRepository->delete($id);
    }
}
