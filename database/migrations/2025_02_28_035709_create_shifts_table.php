<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained();
            $table->date('day_since');
            $table->date('day_until');
            $table->integer('val1'); 
            $table->integer('val2'); 
            $table->integer('n_workers');
            $table->integer('salario_base');
            $table->integer('total_presupuesto'); 
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
        Schema::dropIfExists('shifts');
    }
};
