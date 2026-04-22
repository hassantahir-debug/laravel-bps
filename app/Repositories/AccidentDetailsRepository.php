<?php

namespace App\Repositories;

use App\Models\AccidentDetails;

class AccidentDetailsRepository extends BaseRepository
{
    public function __construct(AccidentDetails $model)
    {
        parent::__construct($model);
    }
}
