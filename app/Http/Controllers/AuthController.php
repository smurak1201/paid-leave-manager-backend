<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class AuthController extends Controller
{
    // ログインAPI
    public function login(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'password' => 'required|string',
        ]);
        $employee = Employee::where('employee_id', $request->employee_id)->first();
        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json(['message' => 'IDまたはパスワードが正しくありません'], 401);
        }
        $token = $employee->createToken('api-token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'employee_id' => $employee->employee_id,
            'role' => $employee->role,
        ]);
    }

    // ログアウトAPI
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ログアウトしました']);
    }
}
