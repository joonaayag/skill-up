<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('school_projects', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('author');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->date('creation_date');
            $table->text('description');

            $table->enum('tags', [
                'TFG', 'TFM', 'Tesis', 'Individual', 'Grupal',
                'Tecnología', 'Ciencias', 'Artes', 'Ingeniería'
            ]);

            $table->enum('general_category', [
                'Administración y negocio', 'Ciencia y salud',
                'Comunicación', 'Diseño y comunicación',
                'Educación', 'Industria', 'Otro',
                'Tecnología y desarrollo'
            ]);

            $table->string('image')->nullable();
            $table->string('link')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_projects');
    }
};
