<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementOutputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_outputs', function (Blueprint $table) {
            $table->id();
            $table->integer('movement_id');
            $table->string('customer');
            $table->integer('movement_type')->nullable();
            $table->double('amount');
            $table->string('disperser')->nullable();
            $table->string('bco_cta_disperser');
            $table->string('bco_cta_customer');
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('movement_outputs');
    }
}
