<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no');
            $table->string('type');
            $table->string('purpose');
            $table->string('remarks');
            $table->foreignId('user_id')->references('id')->on('users')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('department_id')->references('id')->on('departments')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supp_docs_id')->nullable()->references('id')->on('support_documents')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->integer('w_PO')->nullable();
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
        Schema::dropIfExists('purchase_requests');
    }
}
