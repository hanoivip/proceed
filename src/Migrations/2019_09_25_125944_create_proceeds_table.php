<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProceedsTable extends Migration
{
    public function up()
    {
        Schema::create('proceeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('proceed')->default(0);
            $table->integer('exchange_count')->default(0);
            $table->integer('excahnge_total')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proceeds');
    }
}
