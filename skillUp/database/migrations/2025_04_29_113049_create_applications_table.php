<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('job_offers')->onDelete('cascade');
    
            $table->string('candidate_name');
            $table->string('position_applied');
            $table->text('application_reason');
            $table->string('cv')->nullable(); // Route to file cv in storage
    
            $table->enum('state', ['nueva', 'en revisión', 'aceptado', 'rechazado']);
            $table->date('application_date');
    
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
