<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\procedureCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CodeController extends Controller
{
    public function index()
    {
        return procedureCode::select('id', 'code', 'description', 'price')->get();
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|integer',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0|decimal:0,2'
            ]);
            $procedureCodestored = procedureCode::create($validated);
            return response()->json(["message" => "created successfully", "procedureCode" => $procedureCodestored], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 200);
        }
    }
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|integer',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0|decimal:0,2'
            ]);

            $procedureCode = procedureCode::findOrFail($id);
            $procedureCode->update($validated);
            return response()->json($procedureCode, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $code = procedureCode::findOrFail($id);
        $code->delete();
        return response()->json($code, 200);
    }
}
