# 有給休暇管理アプリ（Laravel バックエンド）

このリポジトリは、日本の労働基準法・厚生労働省ガイドラインに準拠した有給休暇管理 Web アプリの API バックエンド（Laravel）です。
従業員ごとの有給付与・消化・繰越・時効消滅・最大保有日数・FIFO 消化順序などのロジックを自動計算し、フロントエンド（React/Vite）と RESTful API で連携します。
主要な業務ロジック・バリデーション・API 設計はすべて統一された設計コメント・型定義・責務分離に基づき実装されています。

---

## クイックスタート

1. `composer install` で依存パッケージをインストール
2. `.env` 設定（DB 接続・APP_KEY 生成など）
3. `php artisan migrate --seed` で DB 初期化・マスターデータ投入
4. `php artisan serve` でローカル API サーバー起動

---

## 主な API 機能

-   **従業員管理 API**
    -   従業員の追加・編集・削除・一覧取得
-   **有給休暇管理 API**
    -   勤続年数に応じた有給付与日数の自動計算（正社員モデル・日本法令準拠）
    -   有給取得日リストの追加・削除
    -   有給消化・繰越・2 年時効消滅ロジック
    -   最大保有日数 40 日、FIFO（先入れ先出し）消化順序に対応
-   **バリデーション・エラーハンドリング**
    -   FormRequest（例: EmployeeRequest）によるバリデーション共通化、重複・存在チェック、DB 例外処理
    -   API エラー・バリデーションエラーの統一レスポンス
-   **API 設計**
    -   RESTful なエンドポイント設計（従業員・有給取得日・付与マスター API すべて）
    -   JSON レスポンス・エラー時のメッセージ統一
    -   CORS・ネットワーク・型整合性も考慮

---

## 技術スタック

-   Laravel 10+
-   PHP 8.1+
-   SQLite/MySQL/PostgreSQL（任意の DB）

---

## 主なファイル構成・役割

-   `routes/api.php` : API ルーティング定義（全エンドポイント集約）
-   `app/Http/Controllers/EmployeesController.php` : 従業員管理 API コントローラ
-   `app/Http/Controllers/LeaveUsageController.php` : 有給付与・消化・繰越・時効消滅・最大保有日数・FIFO 消化順序ロジック（日本法令準拠）
-   `app/Http/Controllers/LeaveGrantMasterController.php` : 付与マスター API コントローラ
-   `app/Http/Requests/EmployeeRequest.php` : 従業員追加・編集用バリデーション（FormRequest）
-   `app/Models/Employee.php`, `LeaveUsage.php`, `LeaveGrantMaster.php` : Eloquent モデル定義
-   `database/seeders/LeaveGrantMasterSeeder.php` : 付与マスター初期データ投入用 Seeder
-   `database/migrations/` : テーブル構造定義（従業員・有給・付与マスター）
-   `sample/` : サンプル API・ダミーデータ（本番未使用、不要なら削除可）
-   `backend_learning_guide.md` : Laravel バックエンド学習ガイド（設計意図・実装例解説）

---

## 実装済みロジック（2025 年 7 月現在）

-   勤続年数に応じた有給付与日数の自動計算（正社員モデル、日本法令準拠）
-   付与日ごとに 2 年の有効期限管理
-   前回付与分の残日数（最大 20 日）を繰越
-   最大保有日数 40 日（付与＋繰越の合計）
-   FIFO（先入れ先出し）消化順序
-   有効期限切れ分の自動失効
-   日単位での有給取得・管理
-   API/型/コメントの統一・リファクタリング

---

## 未対応・追加実装が必要な主なロジック

-   年 5 日取得義務（2019 年法改正）
-   出勤率 8 割判定による付与可否
-   雇用形態別付与（パート・短時間労働者等）
-   時間単位・半日単位有給
-   付与基準日（入社日以外の基準日管理）
-   失効日数の明示・管理
-   特別休暇・その他休暇との区別

これらの要件が必要な場合は、個別に追加実装が必要です。

---

## 学習・実務での活用ポイント

-   コントローラ・モデル・FormRequest（バリデーション）・業務ロジックの分離、統一コメント・型定義・責務分離の実例として、Laravel API 設計・法令準拠ロジックの学習に最適です。
-   設計コメントや型定義を参考に、実務・他プロジェクトへの応用も可能です。
-   詳細は `backend_learning_guide.md` を参照してください。

---

## ライセンス

MIT
