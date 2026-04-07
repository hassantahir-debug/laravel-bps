<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\procedureCode;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function index()
    {
        return procedureCode::select('id', 'code', 'description', 'price')->get();
    }
}
