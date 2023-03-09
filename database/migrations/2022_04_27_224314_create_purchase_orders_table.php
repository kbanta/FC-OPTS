<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no');
            $table->string('pr_no');
            $table->string('status');
            $table->string('createdDate')->nullable();
            $table->string('orderDate')->nullable();
            $table->string('paymentTerm')->nullable();
            $table->string('preparedBy')->nullable();
            $table->string('preparedDate')->nullable();
            $table->string('verifiedBy')->nullable();
            $table->string('verifiedDate')->nullable();
            $table->string('approvedBy')->nullable();
            $table->string('approvedDate')->nullable();
            $table->string('approved2By')->nullable();
            $table->string('approved3By')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
}
