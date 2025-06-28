<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        try {
            $employees = Employee::all();
            return response()->json($employees, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ]);

        try {
            Employee::create($validated);
            return response()->json(['result' => 'ok'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $id,
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ]);

        try {
            $employee = Employee::findOrFail($id);
            $employee->update($validated);
            return response()->json(['result' => 'ok'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            return response()->json(['result' => 'ok'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }
}
