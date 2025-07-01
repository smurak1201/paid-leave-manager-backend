<?php

// =====================================================
// LeaveUsageController.php
// -----------------------------------------------------
// このコントローラは「有給休暇の取得・消化履歴」APIの処理を担当します。
// 主な役割:
//   - 有給取得履歴の一覧取得
//   - 有給取得日の追加・削除
//   - 有給休暇の残日数・消化履歴サマリ計算
//   - 日本の法制度（最大40日・FIFO消化・2年有効等）に準拠したロジック
//
// 【Laravel初心者向けポイント】
// ・コントローラは「1つのリソース（例: 有給履歴）」ごとに作成し、APIルートから呼び出されます。
// ・Request/Responseの型やバリデーション、DB操作、エラーハンドリングの基本例としても参考になります。
// ・制度ロジックの詳細は showSummary メソッド内に実装されています。
// =====================================================

namespace App\Http\Controllers;

use App\Models\LeaveUsage;
use App\Models\Employee;
use App\Models\LeaveGrantMaster;
use Illuminate\Http\Request;
use Exception;

/**
 * LeaveUsageController
 * - 有給休暇の取得・消化履歴APIを担当
 * - 一覧取得・追加・削除・サマリ計算など
 * - 日本法令準拠のロジック例としても学習に最適
 */
class LeaveUsageController extends Controller
{
    /**
     * 有給取得履歴の一覧取得
     */
    public function index()
    {
        try {
            $usages = LeaveUsage::orderBy('employee_id')->orderBy('used_date')->get(['employee_id', 'used_date']);
            return response()->json($usages, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 有給取得日の追加
     * - バリデーション・重複チェック・DB登録
     */
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

    /**
     * 有給取得日の更新
     * - バリデーション・DB更新
     */
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

    /**
     * 有給取得日の削除
     * - バリデーション・DB削除
     */
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

    /**
     * 有給休暇のサマリ情報取得
     * - バリデーション・サマリ計算ロジック
     */
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
        $carry_over = 0;
        if (count($grant_details) > 1) {
            $carry_over = min($prev_remain, 20);
        } else {
            $carry_over = 0;
        }
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
        // 残日数計算用: 最新とその1つ前のgrant分のused_datesをユニーク化してカウント
        $used_for_remain = 0;
        $grant_count = count($grant_details);
        if ($grant_count >= 2) {
            $latest_used = $grant_details[$grant_count - 1]['used_dates'];
            $prev_used = $grant_details[$grant_count - 2]['used_dates'];
            $all_used = array_unique(array_merge($latest_used, $prev_used));
            $used_for_remain = count($all_used);
        } elseif ($grant_count === 1) {
            $used_for_remain = count($grant_details[0]['used_dates']);
        }
        // 残日数は「今年の付与日数＋繰越日数」の合計が20日を超える場合は20日を上限とする
        $max_remain = min($grant_this_year + $carry_over, 20);
        $remain = $max_remain - $used_for_remain;

        // FIFO消化順序・最大保有日数40日対応
        // 1. grant_detailsを付与日昇順で並べる（既に昇順）
        // 2. すべての消化日を昇順で取得
        $all_grant_details = $grant_details;
        $all_used_dates = [];
        foreach ($grant_details as $g) {
            $all_used_dates = array_merge($all_used_dates, $g['used_dates']);
        }
        $all_used_dates = array_values(array_unique($all_used_dates));
        sort($all_used_dates);
        // 3. 付与分ごとに消化日を古い順から割り当てていく
        $fifo_remain = [];
        $used_idx = 0;
        foreach ($grant_details as $i => $g) {
            $days = $g['days'];
            $used = 0;
            $used_dates = [];
            while ($used_idx < count($all_used_dates) && $used < $days) {
                $used_dates[] = $all_used_dates[$used_idx];
                $used++;
                $used_idx++;
            }
            $fifo_remain[] = $days - $used;
        }
        // 4. 有効期限切れ分を除外し、未消化分の合計を算出
        $now = new \DateTime($today);
        $total_remain = 0;
        foreach ($grant_details as $i => $g) {
            $expire = (new \DateTime($g['grant_date']))->modify('+2 years');
            if ($now < $expire) {
                $total_remain += $fifo_remain[$i];
            }
        }
        // 5. 最大保有日数40日で制限
        $remain = min($total_remain, 40);
        // used（消化済み日数）は有効な消化日数
        $used_valid = count($all_used_dates) - ($remain < 0 ? abs($remain) : 0);
        if ($used_valid < 0) $used_valid = 0;

        return response()->json([
            'grantThisYear' => $grant_this_year,
            'carryOver' => $carry_over,
            'used' => $used_valid,
            'remain' => $remain < 0 ? 0 : $remain,
            'grantDetails' => $grant_details,
            'usedDates' => $used_dates_valid,
        ], 200);
    }

    /**
     * 付与日リスト生成関数
     * - 入社日から今日までの付与日を計算
     * - 6.5年以降は毎年20日付与
     */
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
