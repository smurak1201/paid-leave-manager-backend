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
            $usages = LeaveUsage::orderBy('employee_id')->orderBy('used_date')->get(['employee_id', 'used_date']);
            return response()->json($usages, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'used_date' => ['required', 'date', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
        ]);

        $employee = Employee::where('employee_id', $validated['employee_id'])->first();
        if (!$employee) {
            return response()->json(['error' => '存在しない従業員IDです'], 400);
        }
        if ($validated['used_date'] < $employee->joined_at) {
            return response()->json(['error' => '消化日は入社日以降の日付を指定してください'], 400);
        }
        // 重複チェック
        $exists = LeaveUsage::where('employee_id', $employee->id)
            ->where('used_date', $validated['used_date'])
            ->exists();
        if ($exists) {
            return response()->json(['error' => 'この従業員のこの日は既に登録されています'], 400);
        }
        try {
            LeaveUsage::create([
                'employee_id' => $employee->id,
                'used_date' => $validated['used_date'],
            ]);
            return response()->json(['result' => 'ok'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'used_date' => ['required', 'date', 'regex:/^\\d{4}-\\d{2}-\\d{2}$/'],
        ]);
        $employee = Employee::where('employee_id', $validated['employee_id'])->first();
        if (!$employee) {
            return response()->json(['error' => '存在しない従業員IDです'], 400);
        }
        $leaveUsage = LeaveUsage::findOrFail($id);
        $leaveUsage->employee_id = $employee->id;
        $leaveUsage->used_date = $validated['used_date'];
        $leaveUsage->save();
        return response()->json($leaveUsage);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'used_date' => ['required', 'date', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
        ]);
        $employee = Employee::where('employee_id', $validated['employee_id'])->first();
        if (!$employee) {
            return response()->json(['error' => '存在しない従業員IDです'], 400);
        }
        try {
            LeaveUsage::where('employee_id', $employee->id)
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
        $last_row = end($master);
        reset($master);
        foreach ($master as $row) {
            $grant_date = clone $start;
            $grant_date->modify('+' . $row['months'] . ' months');
            if ($grant_date > $now) break;
            $grants[] = [
                'grant_date' => $grant_date->format('Y-m-d'),
                'days' => (int)$row['days'],
            ];
        }
        // 78ヶ月（6.5年）以降は毎年20日付与
        if ($last_row) {
            $last_months = $last_row['months'];
            $last_days = (int)$last_row['days'];
            $first_last_grant = clone $start;
            $first_last_grant->modify('+' . $last_months . ' months');
            // 6.5年以降、毎年1回20日付与
            $next_grant = clone $first_last_grant;
            while ($next_grant <= $now) {
                // 既存付与日と重複しない場合のみ追加
                $exists = false;
                foreach ($grants as $g) {
                    if ($g['grant_date'] === $next_grant->format('Y-m-d')) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $grants[] = [
                        'grant_date' => $next_grant->format('Y-m-d'),
                        'days' => $last_days,
                    ];
                }
                $next_grant->modify('+1 year');
            }
        }
        // 付与日で昇順ソート
        usort($grants, function ($a, $b) {
            return strcmp($a['grant_date'], $b['grant_date']);
        });
        return $grants;
    }
}
