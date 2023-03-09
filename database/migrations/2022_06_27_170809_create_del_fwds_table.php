<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelFwdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('del_fwds', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->integer('isReceived')->nullable();
            $table->string('receivedDate')->nullable();
            $table->integer('isForwarded')->nullable();
            $table->string('forwardedDate')->nullable();
            $table->integer('isApproved')->nullable();
            $table->string('approvedDate')->nullable();
            $table->integer('isReqReceived')->nullable();
            $table->string('reqreceivedDate')->nullable();
            $table->string('delivery_no');
            $table->string('forward_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('del_fwds');
    }
}
