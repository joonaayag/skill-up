<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\SchoolProject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'jonay@gmail.com'],
            [
                'name' => 'Jonay',
                'password' => Hash::make('password'),
                'role' => 'usuario',
            ]
        );
        User::firstOrCreate(
            ['email' => 'james@gmail.com'],
            [
                'name' => 'James',
                'password' => Hash::make('password'),
                'role' => 'empresa',
            ]
        );
        User::firstOrCreate(
            ['email' => 'alberto@gmail.com'],
            [
                'name' => 'Alberto',
                'password' => Hash::make('password'),
                'role' => 'profesor',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'pedro@gmail.com'],
            [
                'name' => 'Pedro',
                'password' => Hash::make('password'),
                'role' => 'usuario',
            ]
        );

        Project::insert([
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amail la cuquis',
                'image' => null,
                'description' => 'Me cago en la puta.',
                'author_id' => $user->id,
                'tags' => 'Grupal',
                'general_category' => 'Otro',
                'creation_date' => '2022-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gestor de tareas colaborativo',
                'image' => null,
                'description' => 'Aplicación web para asignación y seguimiento de tareas entre equipos.',
                'author_id' => $user->id,
                'tags' => 'Individual',
                'general_category' => 'Tecnología y desarrollo',
                'creation_date' => '2023-09-01',
                'created_at' => now(),
                'updated_at' => now(),
            ]
            
        ]);

        $profesor = User::where('email', 'alberto@gmail.com')->first();

SchoolProject::insert([
    [
        'title' => 'Sistema de gestión académica',
        'author' => 'Laura Pérez',
        'creation_date' => '2023-05-10',
        'description' => 'Proyecto escolar enfocado en digitalizar la gestión de clases y calificaciones.',
        'tags' => 'TFG',
        'general_category' => 'Tecnología y desarrollo',
        'image' => null,
        'file' => null,
        'link' => null,
        'user_id' => $profesor->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'title' => 'Análisis de datos climáticos',
        'author' => 'Carlos López',
        'creation_date' => '2023-06-15',
        'description' => 'Estudio basado en el comportamiento del clima en distintas regiones.',
        'tags' => 'Individual',
        'general_category' => 'Ciencia y salud',
        'image' => null,
        'file' => null,
        'link' => null,
        'user_id' => $profesor->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'title' => 'Aplicación web para biblioteca',
        'author' => 'Ana Gómez',
        'creation_date' => '2024-01-20',
        'description' => 'Una plataforma que permite gestionar préstamos y reservas de libros.',
        'tags' => 'TFM',
        'general_category' => 'Educación',
        'image' => null,
        'file' => null,
        'link' => null,
        'user_id' => $profesor->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'title' => 'Estudio de energías renovables',
        'author' => 'David Martín',
        'creation_date' => '2024-03-11',
        'description' => 'Investigación sobre el impacto de energías limpias en comunidades rurales.',
        'tags' => 'Tesis',
        'general_category' => 'Industria',
        'image' => null,
        'file' => null,
        'link' => null,
        'user_id' => $profesor->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'title' => 'Campaña de concienciación ambiental',
        'author' => 'Lucía Fernández',
        'creation_date' => '2024-04-05',
        'description' => 'Proyecto de comunicación y diseño para concientizar sobre el reciclaje.',
        'tags' => 'Grupal',
        'general_category' => 'Diseño y comunicación',
        'image' => null,
        'file' => null,
        'link' => null,
        'user_id' => $profesor->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);


    }
}
