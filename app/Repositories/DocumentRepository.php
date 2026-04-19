<?php

namespace App\Repositories;

use App\Models\document;

class DocumentRepository extends BaseRepository
{
    public function __construct(document $model)
    {
        parent::__construct($model);
    }

    /**
     * Create document with data
     */
    public function createDocument(array $data)
    {
        return $this->create($data);
    }
}
