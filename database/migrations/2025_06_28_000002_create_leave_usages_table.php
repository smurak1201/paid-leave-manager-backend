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
      $table->unsignedBigInteger('employee_id');
      $table->date('used_date');
      $table->timestamps();

      $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
    });
  }

  public function down()
  {
    Schema::dropIfExists('leave_usages');
  }
}
