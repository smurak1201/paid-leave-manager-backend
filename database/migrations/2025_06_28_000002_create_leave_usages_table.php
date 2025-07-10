
<?php
// =====================================================
// create_leave_usages_table.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】有給取得履歴テーブル用マイグレーション
// -----------------------------------------------------
// ▼主な役割
//   - 有給取得履歴を保存するテーブルを作成
// ▼設計意図
//   - 従業員ID・消化日・外部キー制約で管理
// ▼使い方
//   - migrateコマンドでDBテーブル作成
// =====================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveUsagesTable extends Migration
{
    public function up()
    {
        Schema::create('leave_usages', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 32);
            $table->date('used_date');
            $table->timestamps();
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_usages');
    }
}
