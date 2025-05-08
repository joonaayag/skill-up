<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('image')->nullable();
            $table->text('description');

            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');

            $table->enum('tags', [
                'TFG', 'TFM', 'Tesis', 'Individual', 'Grupal',
                'Tecnología', 'Ciencias', 'Artes', 'Ingeniería'
            ]);

            $table->enum('general_category', [
                'Tecnología y desarrollo', 'Diseño y comunicación',
                'Administración y negocio', 'Comunicación',
                'Educación', 'Ciencia y salud', 'Industria', 'Otro'
            ]);
            $table->string('link')->nullable();

            $table->date('creation_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
