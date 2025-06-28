<?php
// =============================
// employees.php: 従業員一覧・追加・編集・削除API
// =============================
//
// 役割:
// ・従業員データ（employeesテーブル）の一覧取得・追加・編集・削除を行うAPI
//
// 設計意図:
// ・REST APIとしてシンプルに設計
// ・リクエストはJSONで受け取り、レスポンスもJSONで返す
// ・エラー時はJSONで詳細を返す
// ・初学者でも流れが追いやすいよう詳細コメント
//
// 使い方:
// ・GET:   一覧取得
// ・POST:  mode=add/edit/delete で追加・編集・削除
//
// フロントエンドからJSONでリクエスト・レスポンス

require_once __DIR__ . '/api_common.php'; // API共通処理（ヘッダー・エラー処理）
require_once __DIR__ . '/db.php';         // DB接続

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  $mode = isset($input['mode']) ? $input['mode'] : '';

  if ($mode === 'add') {
    // 追加: 必須パラメータ取得・重複チェック・INSERT
    $employee_id = $input['employee_id'] ?? '';
    $last_name = $input['last_name'] ?? '';
    $first_name = $input['first_name'] ?? '';
    $joined_at = $input['joined_at'] ?? '';
    if (!$employee_id || !$last_name || !$first_name || !$joined_at) {
      api_error('全項目必須です', 400);
    }
    $stmt = $pdo->prepare('SELECT id FROM employees WHERE employee_id = ?');
    $stmt->execute([$employee_id]);
    if ($stmt->fetch()) {
      api_error('この従業員IDは既に登録されています', 400);
    }
    try {
      $stmt = $pdo->prepare('INSERT INTO employees (employee_id, last_name, first_name, joined_at) VALUES (?, ?, ?, ?)');
      $stmt->execute([$employee_id, $last_name, $first_name, $joined_at]);
      echo json_encode(['result' => 'ok']);
    } catch (PDOException $e) {
      api_error('DBエラー: ' . $e->getMessage());
    }
    exit;
  } elseif ($mode === 'edit') {
    // 編集: 必須パラメータ取得・UPDATE
    $id = $input['id'] ?? 0; // PK
    $employee_id = $input['employee_id'] ?? '';
    $last_name = $input['last_name'] ?? '';
    $first_name = $input['first_name'] ?? '';
    $joined_at = $input['joined_at'] ?? '';
    if (!$id || !$employee_id || !$last_name || !$first_name || !$joined_at) {
      api_error('全項目必須です', 400);
    }
    try {
      $stmt = $pdo->prepare('UPDATE employees SET employee_id=?, last_name=?, first_name=?, joined_at=? WHERE id=?');
      $stmt->execute([$employee_id, $last_name, $first_name, $joined_at, $id]);
      echo json_encode(['result' => 'ok']);
    } catch (PDOException $e) {
      api_error('DBエラー: ' . $e->getMessage());
    }
    exit;
  } elseif ($mode === 'delete') {
    // 削除: employee_id指定で従業員と有給履歴を削除
    $employee_id = $input['employee_id'] ?? '';
    if (!$employee_id) {
      api_error('employee_idは必須です', 400);
    }
    try {
      $stmt = $pdo->prepare('SELECT id FROM employees WHERE employee_id=?');
      $stmt->execute([$employee_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
        api_error('該当従業員が見つかりません', 404);
      }
      $pk = $row['id'];
      $stmt = $pdo->prepare('DELETE FROM leave_usages WHERE employee_id=?');
      $stmt->execute([$pk]);
      $stmt = $pdo->prepare('DELETE FROM employees WHERE id=?');
      $stmt->execute([$pk]);
      echo json_encode(['result' => 'ok']);
    } catch (PDOException $e) {
      api_error('DBエラー: ' . $e->getMessage());
    }
    exit;
  }
}

// 一覧取得（GET）
try {
  $stmt = $pdo->query('SELECT id, employee_id, last_name, first_name, joined_at FROM employees ORDER BY id');
  $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($employees, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
  api_error('DBエラー: ' . $e->getMessage());
}
