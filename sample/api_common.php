<?php
// =============================
// api_common.php: API用の共通処理（ヘッダー・エラーハンドリング）
// =============================
//
// 役割:
// ・すべてのAPIファイルでrequire_onceして使う共通処理
// ・CORS対応やContent-Typeヘッダーの設定、エラーハンドリング関数を提供
//
// 設計意図:
// ・APIの共通処理を一元化し、各APIファイルをシンプルに保つ
// ・エラー時はJSONで詳細を返す
// ・初学者でも流れが追いやすいよう詳細コメント
//
// 使い方:
// ・require_onceでこのファイルを読み込む
// ・api_error()関数でエラー時のレスポンスを統一

// CORS・Content-Typeヘッダー
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// CORSプリフライト対応
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// 共通エラーハンドリング関数
function api_error($message, $code = 500)
{
  http_response_code($code);
  echo json_encode(['error' => $message]);
  exit;
}
