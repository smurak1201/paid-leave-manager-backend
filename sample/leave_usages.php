<?php
// =============================
// leave_usages.php: 有給消化履歴一覧API
// =============================
//
// 役割:
// ・全従業員の有給消化履歴（leave_usagesテーブル）を取得し、JSONで返すAPI
//
// 設計意図:
// ・REST APIとしてシンプルに設計
// ・エラー時はJSONで詳細を返す
// ・初学者でも流れが追いやすいよう詳細コメント
//
// 使い方:
// ・フロントエンドからGETリクエストで呼び出すと、
//   [{ employee_id: ..., used_date: ... }, ...] の配列が返る

require_once __DIR__ . '/api_common.php'; // API共通処理（ヘッダー・エラー処理）
require_once __DIR__ . '/db.php';         // DB接続

try {
  // leave_usagesテーブルから全ての消化履歴を取得
  $stmt = $pdo->query('SELECT employee_id, used_date FROM leave_usages ORDER BY employee_id, used_date');
  $usages = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // 取得したデータをJSON形式で返す（日本語もそのまま、整形付き）
  echo json_encode($usages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
  // DBエラー時はエラーメッセージをJSONで返す
  api_error('DBエラー: ' . $e->getMessage());
}
