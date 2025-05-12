@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <x-heading level="h1" class="mb-6 text-3xl font-bold text-gray-800 dark:text-white">
            Panel de Administración
        </x-heading>

        <x-heading level="h3" class="mb-10 text-xl font-medium text-gray-700 dark:text-themeLightGray">
            Bienvenido {{ auth()->user()->name . ' ' . auth()->user()->last_name }}
        </x-heading>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
            <a href="{{ route('admin.projects') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">Gestionar Proyectos</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">Visualiza y administra los proyectos generales.
                </p>
            </a>

            <a href="{{ route('admin.school_projects') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">Gestionar Proyectos Escolares</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">Administra los proyectos realizados en centros
                    educativos.</p>
            </a>

            <a href="{{ route('admin.users') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">Gestionar Usuarios</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">Gestiona perfiles de estudiantes, profesores y
                    empresas.</p>
            </a>

            <a href="{{ route('admin.offers') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">Gestionar Ofertas</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">Controla las ofertas laborales publicadas por
                    empresas.</p>
            </a>
        </div>
    </div>

@endsection