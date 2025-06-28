<?php

namespace App\Http\Controllers;

use App\Models\LeaveGrantMaster;
use Illuminate\Http\Request;

class LeaveGrantMasterController extends Controller
{
  public function index()
  {
    try {
      $master = LeaveGrantMaster::orderBy('months')->get();
      return response()->json($master, 200);
    } catch (Exception $e) {
      return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
    }
  }

  public function store(Request $request)
  {
    $leaveGrantMaster = LeaveGrantMaster::create($request->all());
    return response()->json($leaveGrantMaster, 201);
  }

  public function update(Request $request, $id)
  {
    $leaveGrantMaster = LeaveGrantMaster::findOrFail($id);
    $leaveGrantMaster->update($request->all());
    return response()->json($leaveGrantMaster);
  }

  public function destroy($id)
  {
    LeaveGrantMaster::destroy($id);
    return response()->json(null, 204);
  }
}
