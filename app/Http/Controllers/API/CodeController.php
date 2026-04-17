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
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|integer',
                'description' => 'required|string'

            ]);
            $procedureCode = procedureCode::findOrFail($id);
            $procedureCode::update($validated);
            return response()->json($procedureCode, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
