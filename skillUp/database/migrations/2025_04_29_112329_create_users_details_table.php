<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->date('birth_date')->nullable();
            $table->string('current_course')->nullable();
            $table->string('specialization')->nullable();
            $table->string('educational_center')->nullable();
            $table->string('department')->nullable();

            $table->string('validation_document')->nullable();
            $table->string('cif')->nullable();
            $table->string('address')->nullable();
            $table->string('sector')->nullable();
            $table->string('website')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_details');
    }
};

