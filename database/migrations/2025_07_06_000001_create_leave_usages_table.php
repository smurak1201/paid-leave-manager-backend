<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_usage', function (Blueprint $table) {
            $table->id();
            // 業務ID（employees.employee_id）を参照
            $table->unsignedInteger('employee_id');
            $table->date('used_date');
            $table->timestamps();
            // 外部キー制約（業務ID）
            $table->foreign('employee_id')
                ->references('employee_id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_usage');
    }
};
