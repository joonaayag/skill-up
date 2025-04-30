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

            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description');

            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');

            $table->enum('category', [
                'TFG', 'TFM', 'Tesis', 'Individual', 'Grupal',
                'Tecnología', 'Ciencias', 'Artes', 'Ingeniería'
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
