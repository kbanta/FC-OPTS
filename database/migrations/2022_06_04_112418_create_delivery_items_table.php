<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_no')->nullable();
            $table->string('item_desc')->nullable();
            $table->string('item_brand')->nullable();
            $table->string('item_unit')->nullable();
            $table->integer('item_quantity')->nullable();
            $table->string('po_no');
            $table->bigInteger('staff_id')->nullable();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('delivery_items');
    }
}
