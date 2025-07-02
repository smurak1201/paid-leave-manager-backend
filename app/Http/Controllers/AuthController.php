<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
  // ログインAPI
  public function login(Request $request)
  {
    $request->validate([
      'login_id' => 'required|numeric',
      'password' => 'required|string',
    ]);
    $user = User::where('login_id', $request->login_id)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
      return response()->json(['message' => 'IDまたはパスワードが正しくありません'], 401);
    }
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json([
      'token' => $token,
      'login_id' => $user->login_id,
      'role' => $user->role,
      'employee_id' => $user->employee_id,
    ]);
  }

  // ログアウトAPI
  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'ログアウトしました']);
  }
}
