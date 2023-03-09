<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id('id');
            $table->string('pr_no');
            $table->string('beggining')->nullable();
            $table->string('ending')->nullable();
            $table->string('unit');
            $table->string('quantity');
            $table->string('item_desc');
            $table->integer('item_id')->nullable();
            $table->integer('checkitemby')->nullable();
            $table->integer('sel')->nullable();
            $table->integer('chk_item')->nullable();
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
        Schema::dropIfExists('purchase_request_items');
    }
}
