<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('views')->default(0);
        });

        Schema::table('school_projects', function (Blueprint $table) {
            $table->unsignedInteger('views')->default(0);
        });

        Schema::table('job_offers', function (Blueprint $table) {
            $table->unsignedInteger('views')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('views');
        });

        Schema::table('school_projects', function (Blueprint $table) {
            $table->dropColumn('views');
        });

        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn('views');
        });
    }

};
