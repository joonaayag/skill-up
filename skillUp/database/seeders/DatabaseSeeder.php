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
                'role' => 'Usuario',
            ]
        );
        User::firstOrCreate(
            ['email' => 'james@gmail.com'],
            [
                'name' => 'James',
                'password' => Hash::make('password'),
                'role' => 'Empresa',
            ]
        );
        User::firstOrCreate(
            ['email' => 'alberto@gmail.com'],
            [
                'name' => 'Alberto',
                'password' => Hash::make('password'),
                'role' => 'Profesor',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'pedro@gmail.com'],
            [
                'name' => 'Pedro',
                'password' => Hash::make('password'),
                'role' => 'Usuario',
            ]
        );

        Project::insert([
            [
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Amail la cuquis',
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
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Gestor de tareas colaborativo',
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
                'title' => 'Gestor de tareas colaborativo',
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

        User::create([
            'name' => 'Admin',
            'last_name' => 'Master',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'Admin',
            'avatar' => null,
            'cv' => null,
            'profile' => null,
            'banner' => null,
        ]);


    }
}
