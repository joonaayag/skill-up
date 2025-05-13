<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->text('description');

            $table->enum('sector_category', [
                'Agricultura/Medioambiente',
                'Arte/Cultura',
                'Automoción',
                'Ciberseguridad',
                'Community Manager',
                'Construcción',
                'Coordinación educativa',
                'Diseño gráfico',
                'Datos y analíticas',
                'Desarrollo software',
                'Electricidad/Fontanería',
                'Energía/Renovables',
                'Farmacia',
                'Finanzas y contabilidad',
                'Fotografía/Video',
                'Hostelería/Turismo',
                'IA',
                'Investigación/Laboratorio',
                'Legal/Jurídico',
                'Logística',
                'Mecánica',
                'Medicina/Enfermería',
                'Nutrición',
                'Operario industrial',
                'Orientación',
                'Periodismo',
                'Profesorado',
                'Publicidad',
                'Psicología',
                'Redes y sistemas',
                'RRHH',
                'Seguridad',
                'SEO/SEM',
                'Terapias/Rehabilitación',
                'Traducción',
                'Transporte/Reparto',
                'Ventas',
            ]);

            $table->enum('general_category', [
                'Administración y negocio',
                'Ciencia y salud',
                'Comunicación',
                'Diseño y comunicación',
                'Educación',
                'Industria',
                'Otro',
                'Tecnología y desarrollo',
            ]);

            $table->enum('state', ['Abierta', 'Cerrada']);

            $table->foreignId('company_id')->constrained('users')->onDelete('cascade');

            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_offers');
    }
};
