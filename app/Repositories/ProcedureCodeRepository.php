<?php

namespace App\Repositories;

use App\Models\ProcedureCode;

class ProcedureCodeRepository extends BaseRepository
{
    public function __construct(ProcedureCode $model)
    {
        parent::__construct($model);
    }
}
