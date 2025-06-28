<?php
// =============================
// leave_usage_delete.php: 有給消化日削除API
// =============================
//
// 役割:
// ・従業員の有給消化日（leave_usagesテーブルの1行）を削除するAPI
// ・従業員コード(employee_id)と消化日(used_date)を指定して削除
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
  // 3. 指定された従業員コード・消化日で1件削除
  $stmt = $pdo->prepare('DELETE FROM leave_usages WHERE employee_id = ? AND used_date = ?');
  $stmt->execute([$employee_id, $used_date]);
  // 4. 正常終了レスポンス
  echo json_encode(['result' => 'ok']);
} catch (PDOException $e) {
  // 5. DBエラー時は詳細を返す
  api_error('DBエラー: ' . $e->getMessage(), 500);
}
