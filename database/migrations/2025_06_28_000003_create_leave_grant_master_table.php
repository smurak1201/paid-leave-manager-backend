<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveGrantMasterTable extends Migration
{
    public function up()
    {
        Schema::create('leave_grant_master', function (Blueprint $table) {
            $table->id();
            $table->integer('months');
            $table->integer('days');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_grant_master');
    }
}
