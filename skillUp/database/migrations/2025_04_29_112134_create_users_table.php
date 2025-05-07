<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable(); // should be null for companys
            $table->text('description')->nullable();
            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role', ['pendiente', 'usuario', 'alumno', 'profesor', 'empresa', 'admin']);

            $table->string('avatar')->nullable();
            $table->string('cv')->nullable(); // Route to the CV in storage
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
