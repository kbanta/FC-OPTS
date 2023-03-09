<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForwardedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forwarded_items', function (Blueprint $table) {
            $table->id();
            $table->string('forward_no');
            $table->string('item_desc')->nullable();
            $table->string('item_brand')->nullable();
            $table->string('item_unit')->nullable();
            $table->string('item_quantity')->nullable();
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
        Schema::dropIfExists('forwarded_items');
    }
}
