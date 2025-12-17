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
        Schema::create('personal_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personal_id'); 
            $table->unsignedBigInteger('schedule_id'); 
            $table->unsignedBigInteger('shift_id');  
            $table->string('day_since');
            $table->string('day_until');
            $table->string('date_programation');
            $table->string('turn');
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
        Schema::dropIfExists('personal_shifts');
    }
};
