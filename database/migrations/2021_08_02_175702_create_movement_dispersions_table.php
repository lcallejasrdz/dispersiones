<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementDispersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_dispersions', function (Blueprint $table) {
            $table->id();
            $table->integer('movement_id');
            $table->integer('disperser_id');
            $table->string('disperser_bank_account');
            $table->double('amount');
            $table->integer('destiny_id');
            $table->string('destiny_bank_account');
            $table->integer('final_account')->nullable();
            $table->integer('confirm')->nullable();
            $table->string('folio')->nullable();
            $table->string('receipt')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movement_dispersions');
    }
}
