
<?php
// =====================================================
// create_employees_table.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】従業員テーブル用マイグレーション
// -----------------------------------------------------
// ▼主な役割
//   - 従業員情報を保存するテーブルを作成
// ▼設計意図
//   - employee_id主キー・パスワード/権限/入社日等を管理
// ▼使い方
//   - migrateコマンドでDBテーブル作成
// =====================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('employee_id', 32)->primary(); // 文字列型の主キー
            $table->string('last_name');
            $table->string('first_name');
            $table->date('joined_at');
            $table->string('password');
            $table->string('role')->default('viewer');
            $table->rememberToken()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
