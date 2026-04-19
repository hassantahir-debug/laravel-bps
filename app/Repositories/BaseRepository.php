<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get paginated records
     */
    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Find record by ID
     */
    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     */
    public function update($id, array $data)
    {
        $record = $this->findById($id);
        $record->update($data);
        return $record;
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $record = $this->findById($id);
        $record->delete();
        return $record;
    }

    /**
     * Get query builder instance
     */
    public function query()
    {
        return $this->model->query();
    }
}
