<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('movement_id');
            $table->integer('company_id');
            $table->double('amount');
            $table->string('bank');
            $table->string('account');
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('movement_entries');
    }
}
