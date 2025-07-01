<?php
// =============================
// db.php: データベース接続用の共通ファイル
// =============================
//
// 役割:
// ・MySQLデータベースへの接続を行い、$pdo（PDOインスタンス）を提供する共通ファイル
//
// 設計意図:
// ・すべてのAPIファイルでrequireして使うことで、DB接続処理を一元化
// ・接続エラー時はJSONでエラーメッセージを返し、処理を中断
// ・初学者でも流れが追いやすいよう詳細コメント
//
// 使い方:
// ・require_onceでこのファイルを読み込むと、$pdoが利用可能になる

$host = '172.18.119.226';
$dbname = 'paid_leave_manager';
$user = 'plm_user'; // DBユーザー名
$pass = 'wP[X[dY5UekZ_XWj'; // DBパスワード

try {
  // PDOを使ってMySQLに接続（UTF-8, 例外モード）
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
} catch (PDOException $e) {
  // 接続エラー時は500エラーとJSONでメッセージを返す
  http_response_code(500);
  echo json_encode(['error' => 'DB接続エラー: ' . $e->getMessage()]);
  exit;
}
