@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <x-heading level="h1" class="mb-6 text-3xl font-bold text-gray-800 dark:text-white">
            {{ __('messages.admin.dashboard.title') }}
        </x-heading>

        <x-heading level="h3" class="mb-10 text-xl font-medium text-gray-700 dark:text-themeLightGray">
            {{ __('messages.admin.dashboard.welcome') }} {{ auth()->user()->name . ' ' . auth()->user()->last_name }}
        </x-heading>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
            <a href="{{ route('admin.projects') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.p-management.text') }}</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">{{ __('messages.admin.dashboard.p-management.description') }}
                </p>
            </a>

            <a href="{{ route('admin.school_projects') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.sp-management.text') }}</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">{{ __('messages.admin.dashboard.sp-management.description') }}
                    educativos.</p>
            </a>

            <a href="{{ route('admin.users') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.u-management.text') }}</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">{{ __('messages.admin.dashboard.u-management.description') }}</p>
            </a>

            <a href="{{ route('admin.offers') }}"
                class="block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.o-management.text') }}</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">{{ __('messages.admin.dashboard.o-management.description') }}</p>
            </a>
            <a href="{{ route('admin.comments') }}"
                class="col-span-2 text-center block bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-700 p-6 rounded-lg shadow hover:shadow-md transition hover:border-themeBlue hover:scale-101">
                <h4 class="text-lg font-semibold text-themeBlue mb-2">{{ __('messages.admin.dashboard.c-management.text') }}</h4>
                <p class="text-sm text-gray-600 dark:text-themeLightGray">{{ __('messages.admin.dashboard.c-management.description') }}</p>
            </a>
        </div>
    </div>

@endsection