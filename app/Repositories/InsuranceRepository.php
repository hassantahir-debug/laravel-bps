<?php

namespace App\Repositories;

use App\Models\Insurance;

class InsuranceRepository extends BaseRepository
{
    public function __construct(Insurance $model)
    {
        parent::__construct($model);
    }
}
