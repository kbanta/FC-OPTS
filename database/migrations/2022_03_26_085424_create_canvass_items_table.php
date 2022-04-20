<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanvassItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canvass_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canvass_no')->references('canvass_no')->on('canvasses')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->references('id')->on('items')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('supplier')->nullable();
            $table->float('offered_price')->nullable();
            $table->string('selected')->nullable();
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
        Schema::dropIfExists('canvass_items');
    }
}
