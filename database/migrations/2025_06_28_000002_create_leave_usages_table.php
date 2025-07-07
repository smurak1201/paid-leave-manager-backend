<?php

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
