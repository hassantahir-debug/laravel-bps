<?php

namespace App\Repositories;

use App\Models\procedureCode;

class ProcedureCodeRepository extends BaseRepository
{
    public function __construct(procedureCode $model)
    {
        parent::__construct($model);
    }
}
