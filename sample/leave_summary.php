<?php
// =============================
// leave_summary.php: 指定従業員の有給サマリーAPI
// =============================
//
// 役割:
// ・指定した従業員の有給付与・消化・残日数を集計し、JSONで返すAPI
//
// 設計意図:
// ・REST APIとしてシンプルに設計
// ・エラー時はJSONで詳細を返す
// ・初学者でも流れが追いやすいよう詳細コメント
//
// 使い方:
// ・フロントエンドからGETリクエストで employee_id を指定して呼び出す
//   例: /leave_summary.php?employee_id=123
// ・結果は { grantThisYear, carryOver, used, remain, grantDetails, usedDates } 形式

require_once __DIR__ . '/api_common.php'; // API共通処理（ヘッダー・エラー処理）
require_once __DIR__ . '/db.php';         // DB接続

// employee_idで従業員を特定
if (isset($_GET['employee_id'])) {
  $employee_id = intval($_GET['employee_id']);
  $stmt = $pdo->prepare('SELECT id FROM employees WHERE employee_id = ?');
  $stmt->execute([$employee_id]);
  $row = $stmt->fetch();
  if ($row) {
    $employee_pk = $row['id'];
  } else {
    api_error('従業員が見つかりません', 404);
  }
} else {
  api_error('employee_idは必須です', 400);
}

// 入社日・消化履歴・付与マスタ取得
$emp = $pdo->prepare('SELECT id, joined_at FROM employees WHERE id = ?');
$emp->execute([$employee_pk]);
$employee = $emp->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
  api_error('従業員が見つかりません', 404);
}
$usages = $pdo->prepare('SELECT used_date FROM leave_usages WHERE employee_id = ? ORDER BY used_date');
$usages->execute([$employee_id]); // 従業員コードで検索
$usage_dates = array_column($usages->fetchAll(PDO::FETCH_ASSOC), 'used_date');
$master = $pdo->query('SELECT months, days FROM leave_grant_master ORDER BY months')->fetchAll(PDO::FETCH_ASSOC);

// 付与日リスト生成・有効な付与分・消化日数集計
$today = date('Y-m-d');
$grants = generate_grants($employee['joined_at'], $today, $master);

// 有効な付与分のみ抽出（付与日+2年>今日）
$now = new DateTime($today);
$valid_grants = array_filter($grants, function ($g) use ($now) {
  $expire = new DateTime($g['grant_date']);
  $expire->modify('+2 years');
  return $now < $expire;
});

// 消化日数を古い付与分から順に割当
$grant_this_year = 0;
$carry_over = 0;
$prev_remain = 0;
$grant_details = [];
$latest_grant = null;
$valid_grants_arr = array_values($valid_grants); // 添字を0から詰め直し
$last_idx = count($valid_grants_arr) - 1;
foreach ($valid_grants_arr as $i => $g) {
  $expire = (new DateTime($g['grant_date']))->modify('+2 years');
  $used_dates = [];
  // この付与分の有効期間内の消化日を全て判定
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
    'used_dates' => $used_dates, // 有効期限内のみ
  ];
  // 最新の有効な付与分を記録
  $latest_grant = $g;
  // 最新の1つ前の付与分の残日数を繰越とする
  if ($i === $last_idx - 1) {
    $prev_remain = $remain;
  }
  // 有効な付与分が1つしかない場合、その残日数をprev_remainに
  if ($last_idx === 0) {
    $prev_remain = $remain;
  }
}
// --- 繰越は最新の1つ前の付与分の残日数（最大20日） ---
// ただし有効な付与分が1つしかない場合はその残日数（最大20日）
$carry_over = min($prev_remain, 20);
// used_dates_valid: 全有効期間の消化日をユニーク化
$used_dates_valid = [];
foreach ($grant_details as $g) {
  $used_dates_valid = array_merge($used_dates_valid, $g['used_dates']);
}
$used_dates_valid = array_values(array_unique($used_dates_valid));
$used = count($used_dates_valid); // ユニークな消化日数
// 最新の有効な付与分の日数をgrantThisYearにセット
if ($latest_grant) {
  $grant_this_year = $latest_grant['days'];
}
// grant_details集計後にユニークな消化日数で残日数を計算
$total_granted = 0;
foreach ($grant_details as $g) {
  $total_granted += $g['days'];
}
$remain = $total_granted - $used; // $usedはユニークな消化日数

// 結果をJSONで返す
echo json_encode([
  'grantThisYear' => $grant_this_year,
  'carryOver' => $carry_over,
  'used' => $used,
  'remain' => $remain,
  'grantDetails' => $grant_details,
  'usedDates' => $used_dates_valid, // 有効期限内の消化日一覧を追加
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// 付与日リスト生成関数
function generate_grants($joined_at, $today, $master)
{
  $grants = [];
  $start = new DateTime($joined_at);
  $now = new DateTime($today);
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
