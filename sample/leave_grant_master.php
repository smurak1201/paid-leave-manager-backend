<?php
// =============================
// leave_grant_master.php: 有給付与マスタ一覧API
// =============================
//
// 役割:
// ・勤続月数ごとの有給付与日数マスタ（leave_grant_masterテーブル）を一覧取得するAPI
//
// 設計意図:
// ・REST APIとしてシンプルに設計
// ・エラー時はJSONで詳細を返す
// ・初学者でも流れが追いやすいよう詳細コメント

require_once __DIR__ . '/api_common.php'; // 共通APIヘッダー・エラー処理
require_once __DIR__ . '/db.php';         // DB接続

try {
  // 1. 勤続月数ごとの付与日数マスタを全件取得
  $stmt = $pdo->query('SELECT id, months, days FROM leave_grant_master ORDER BY months');
  $master = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // 2. 取得したデータをJSON形式で返す
  echo json_encode($master, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
  // 3. DBエラー時は詳細を返す
  api_error('DBエラー: ' . $e->getMessage());
}
