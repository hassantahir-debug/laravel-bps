<?php

namespace App\Repositories;

use App\Models\accidentDetails;

class AccidentDetailsRepository extends BaseRepository
{
    public function __construct(accidentDetails $model)
    {
        parent::__construct($model);
    }
}
