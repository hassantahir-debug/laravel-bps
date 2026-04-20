<?php

namespace App\Repositories;

use App\Models\document;
use Illuminate\Support\Facades\Log;

class DocumentRepository extends BaseRepository
{
    public function __construct(document $model)
    {
        parent::__construct($model);
    }

    public function getPaginatedResults($page, $search = null)
    {
        $query = $this->model->select('*');
        if ($search) {
            $query = $query->where('file_name', 'like', '%' . $search . '%')
                ->orWhere('document_type', 'like', '%' . $search . '%');
        }
        return $query->latest()->paginate(10);
    }
    public function createDocument(array $data)
    {
        return $this->create($data);
    }
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
