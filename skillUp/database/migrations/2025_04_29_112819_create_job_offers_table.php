<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->text('description');

            $table->enum('sector_category', [
                'Desarrollo software', 'Ciberseguridad', 'Datos y analíticas', 'IA', 'Redes y sistemas',
                'Publicidad', 'Diseño gráfico', 'Fotografía/Video', 'Finanzas y contabilidad', 'RRHH',
                'Ventas', 'Logística', 'Legal/Jurídico', 'Periodismo', 'Traducción', 'SEO/SEM',
                'Community Manager', 'Profesorado', 'Coordinación educativa', 'Orientación',
                'Medicina/Enfermería', 'Psicología', 'Farmacia', 'Investigación/Laboratorio',
                'Terapias/Rehabilitación', 'Nutrición', 'Construcción', 'Electricidad/Fontanería',
                'Mecánica', 'Operario industrial', 'Energía/Renovables', 'Automoción',
                'Agricultura/Medioambiente', 'Hostelería/Turismo', 'Arte/Cultura', 'Transporte/Reparto', 'Seguridad'
            ]);

            $table->enum('general_category', [
                'Tecnología y desarrollo', 'Diseño y comunicación',
                'Administración y negocio', 'Comunicación',
                'Educación', 'Ciencia y salud', 'Industria', 'Otro'
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
