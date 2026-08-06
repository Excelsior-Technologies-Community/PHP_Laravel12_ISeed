<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seeder_histories', function (Blueprint $table) {

            $table->id();

            $table->string('table_name');

            $table->string('seeder_name');

            $table->integer('records')->default(0);

            $table->string('status')->default('generated');

            $table->timestamps();

        });
    }


    public function down()
    {
        Schema::dropIfExists('seeder_histories');
    }
};