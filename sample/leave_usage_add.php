<?php
// =============================
// leave_usage_add.php: 有給消化日追加API
// =============================
//
// 役割:
// ・従業員の有給消化日（leave_usagesテーブルの1行）を追加するAPI
// ・従業員コード(employee_id)と消化日(used_date)を指定して追加
//
// 設計意図:
// ・REST APIとしてシンプルに設計
// ・エラー時はJSONで詳細を返す
// ・初学者でも流れが追いやすいよう詳細コメント
//
// 使い方:
// ・POSTリクエストでJSONデータ（employee_id, used_date）を送信
// ・正常時は { result: 'ok' } を返す

require_once __DIR__ . '/api_common.php'; // 共通APIヘッダー・エラー処理
require_once __DIR__ . '/db.php';         // DB接続

// 1. リクエストボディ(JSON)を取得・デコード
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
  api_error('JSONデータが不正です', 400);
}
// 2. 必須パラメータ取得・バリデーション
$employee_id = isset($input['employee_id']) ? (int)$input['employee_id'] : 0; // 従業員コード
$used_date = isset($input['used_date']) ? $input['used_date'] : '';           // 消化日(YYYY-MM-DD)

if ($employee_id === 0 || $used_date === '') {
  api_error('employee_idとused_dateは必須です', 400);
}

try {
  // 3. 従業員コードの存在チェックと入社日取得
  $stmt = $pdo->prepare('SELECT employee_id, joined_at FROM employees WHERE employee_id = ?');
  $stmt->execute([$employee_id]);
  $emp = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$emp) {
    api_error('存在しない従業員IDです', 400);
  }
  // 4. used_dateが正しい日付かチェック
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $used_date) || !strtotime($used_date)) {
    api_error('used_dateはYYYY-MM-DD形式の日付で指定してください', 400);
  }
  // 5. 入社日より前の日付はNG
  if ($used_date < $emp['joined_at']) {
    api_error('消化日は入社日以降の日付を指定してください', 400);
  }
  // 6. データベースに登録（従業員コードを直接登録）
  $stmt = $pdo->prepare('INSERT INTO leave_usages (employee_id, used_date) VALUES (?, ?)');
  $stmt->execute([$employee_id, $used_date]);
  // 7. 正常終了レスポンス
  echo json_encode(['result' => 'ok']);
} catch (PDOException $e) {
  if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
    api_error('この従業員のこの日は既に登録されています', 400);
  }
  api_error('DBエラー: ' . $e->getMessage(), 500);
}
