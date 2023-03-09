<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_histories', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no');
            $table->string('action');
            $table->string('isVerified')->nullable();
            $table->string('dateVerified')->nullable();
            $table->string('isCheckfund')->nullable();
            $table->string('dateCheckfund')->nullable();
            $table->string('isApproved')->nullable();
            $table->string('dateApproved')->nullable();
            $table->string('isApproved2')->nullable();
            $table->string('dateApproved2')->nullable();
            $table->integer('isHold')->nullable();
            $table->string('dateHold')->nullable();
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
        Schema::dropIfExists('purchase_request_histories');
    }
}
