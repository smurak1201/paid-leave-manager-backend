<?php

namespace App\Http\Controllers;

use App\Models\LeaveUsage;
use App\Models\Employee;
use App\Models\LeaveGrantMaster;
use Illuminate\Http\Request;
use Exception;

class LeaveUsageController extends Controller
{
    public function index()
    {
        try {
            $usages = LeaveUsage::orderBy('employee_id')->orderBy('used_date')->get();
            return response()->json($usages, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'used_date' => 'required|date',
        ]);

        $employee = Employee::where('employee_id', $validated['employee_id'])->first();
        if ($validated['used_date'] < $employee->joined_at) {
            return response()->json(['error' => '消化日は入社日以降の日付を指定してください'], 400);
        }

        try {
            LeaveUsage::create($validated);
            return response()->json(['result' => 'ok'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $leaveUsage = LeaveUsage::findOrFail($id);
        $leaveUsage->update($request->all());
        return response()->json($leaveUsage);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'used_date' => 'required|date',
        ]);

        try {
            LeaveUsage::where('employee_id', $validated['employee_id'])
                ->where('used_date', $validated['used_date'])
                ->delete();
            return response()->json(['result' => 'ok'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function showSummary(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        $employee = Employee::where('employee_id', $validated['employee_id'])->first();
        if (!$employee) {
            return response()->json(['error' => '従業員が見つかりません'], 404);
        }
        $usage_dates = LeaveUsage::where('employee_id', $employee->id)->orderBy('used_date')->pluck('used_date')->toArray();
        $master = LeaveGrantMaster::orderBy('months')->get(['months', 'days'])->toArray();

        $today = date('Y-m-d');
        $grants = $this->generateGrants($employee->joined_at, $today, $master);

        $now = new \DateTime($today);
        $valid_grants = array_filter($grants, function ($g) use ($now) {
            $expire = new \DateTime($g['grant_date']);
            $expire->modify('+2 years');
            return $now < $expire;
        });
        $grant_this_year = 0;
        $carry_over = 0;
        $prev_remain = 0;
        $grant_details = [];
        $latest_grant = null;
        $valid_grants_arr = array_values($valid_grants);
        $last_idx = count($valid_grants_arr) - 1;
        foreach ($valid_grants_arr as $i => $g) {
            $expire = (new \DateTime($g['grant_date']))->modify('+2 years');
            $used_dates = [];
            foreach ($usage_dates as $u) {
                if ($u >= $g['grant_date'] && $u < $expire->format('Y-m-d')) {
                    $used_dates[] = $u;
                }
            }
            $used_count = count($used_dates);
            $remain = $g['days'] - $used_count;
            $grant_details[] = [
                'grant_date' => $g['grant_date'],
                'days' => $g['days'],
                'used' => $used_count,
                'remain' => $remain,
                'used_dates' => $used_dates,
            ];
            $latest_grant = $g;
            if ($i === $last_idx - 1) {
                $prev_remain = $remain;
            }
            if ($last_idx === 0) {
                $prev_remain = $remain;
            }
        }
        $carry_over = min($prev_remain, 20);
        $used_dates_valid = [];
        foreach ($grant_details as $g) {
            $used_dates_valid = array_merge($used_dates_valid, $g['used_dates']);
        }
        $used_dates_valid = array_values(array_unique($used_dates_valid));
        $used = count($used_dates_valid);
        if ($latest_grant) {
            $grant_this_year = $latest_grant['days'];
        }
        $total_granted = 0;
        foreach ($grant_details as $g) {
            $total_granted += $g['days'];
        }
        $remain = $total_granted - $used;

        return response()->json([
            'grantThisYear' => $grant_this_year,
            'carryOver' => $carry_over,
            'used' => $used,
            'remain' => $remain,
            'grantDetails' => $grant_details,
            'usedDates' => $used_dates_valid,
        ], 200);
    }

    // 付与日リスト生成関数
    private function generateGrants($joined_at, $today, $master)
    {
        $grants = [];
        $start = new \DateTime($joined_at);
        $now = new \DateTime($today);
        foreach ($master as $row) {
            $grant_date = clone $start;
            $grant_date->modify('+' . $row['months'] . ' months');
            if ($grant_date > $now) break;
            $grants[] = [
                'grant_date' => $grant_date->format('Y-m-d'),
                'days' => (int)$row['days'],
            ];
        }
        return $grants;
    }
}
