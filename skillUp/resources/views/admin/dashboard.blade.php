@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <x-heading level="h1" class="mb-6 text-gray-800 dark:text-white">
            {{ __('messages.admin.dashboard.title') }}
        </x-heading>
        @if ($errors->any())
            <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-black dark:text-white">- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-heading level="h3" class="mb-10 text-xl font-medium text-gray-700 dark:text-themeLightGray">
            {{ __('messages.admin.dashboard.welcome') }} {{ auth()->user()->name . ' ' . auth()->user()->last_name }}
        </x-heading>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            @if (auth()->user()->role === 'Admin')
                <a href="{{ route('admin.projects') }}"
                    class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                    <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.p-management.text') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-themeLightGray">
                        {{ __('messages.admin.dashboard.p-management.description') }}</p>
                </a>

                <a href="{{ route('admin.school_projects') }}"
                    class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                    <h4 class="text-lg font-semibold text-themeBlue mb-2">
                        {{ __('messages.admin.dashboard.sp-management.text') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-themeLightGray">
                        {{ __('messages.admin.dashboard.sp-management.description') }}</p>
                </a>
            @endif

            @if (in_array(auth()->user()->role, ['Admin', 'Profesor']))
                <a href="{{ route('admin.users') }}"
                    class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                    <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.u-management.text') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-themeLightGray">
                        {{ __('messages.admin.dashboard.u-management.description') }}</p>
                </a>
            @endif

            @if (auth()->user()->role === 'Admin')
                <a href="{{ route('admin.offers') }}"
                    class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                    <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.o-management.text') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-themeLightGray">
                        {{ __('messages.admin.dashboard.o-management.description') }}</p>
                </a>

                <a href="{{ route('admin.comments') }}"
                    class="md:col-span-2 text-center block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                    <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.c-management.text') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-themeLightGray">
                        {{ __('messages.admin.dashboard.c-management.description') }}</p>
                </a>
            @endif

        </div>

    </div>

@endsection