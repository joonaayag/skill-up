@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">{{ __('messages.admin.offers.title') }}</h1>

        <div class="overflow-x-auto rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-title') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-subtitle') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-sector') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-category') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-state') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offers as $offer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->subtitle }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->sector_category }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->general_category }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ ucfirst($offer->state) }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700 whitespace-nowrap space-x-3">

                                {{-- Editar --}}
                                <div x-data="{ openEdit: false }" class="inline-block" x-cloak>
                                    <button @click="openEdit = true"
                                        class="bg-themeBlue/80 border-2 border-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded transition cursor-pointer">Editar</button>

                                    {{-- Modal Editar --}}
                                    <x-modal :show="'openEdit'" @click.outside="openEdit = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.offers.edit') }}</x-heading>

                                        <form action="{{ route('admin.offers.update', $offer->id) }}" method="POST"
                                            class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-title') }}</label>
                                                <input type="text" name="name" value="{{ $offer->name }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-subtitle') }}</label>
                                                <input type="text" name="subtitle" value="{{ $offer->subtitle }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-description') }}</label>
                                                <textarea name="description" rows="4"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                    required>{{ $offer->description }}</textarea>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-sector') }}</label>
                                                    <input type="text" name="sector_category"
                                                        value="{{ $offer->sector_category }}"
                                                        class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                        required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-category') }}</label>
                                                    <input type="text" name="general_category"
                                                        value="{{ $offer->general_category }}"
                                                        class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                        required>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-state') }}</label>
                                                <select name="state"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:bg-themeBgDark dark:text-white dark:border-gray-600"
                                                    required>
                                                    <option value="abierta" {{ $offer->state === 'abierta' ? 'selected' : '' }}>
                                                        {{ __('messages.admin.offers.status-open') }}</option>
                                                    <option value="cerrada" {{ $offer->state === 'cerrada' ? 'selected' : '' }}>
                                                        {{ __('messages.admin.offers.status-close') }}</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-logo') }}</label>
                                                <input type="text" name="logo" value="{{ $offer->logo }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600">
                                            </div>

                                            <div class="mt-6 flex justify-end gap-4">
                                                <button type="button" @click="openEdit = false"
                                                    class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                    {{ __('messages.button.cancel') }}
                                                </button>
                                                <button type="submit"
                                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer">
                                                    {{ __('messages.button.save-changes') }}
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </div>

                                {{-- Eliminar --}}
                                <div x-data="{ openDelete: false }" class="inline-block" x-cloak>
                                    <button @click="openDelete = true"
                                        class="bg-red-500 border-2 border-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.delete') }}</button>

                                    <x-modal :show="'openDelete'" @click.outside="openDelete = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.offers.delete-confirm') }}</x-heading>
                                        <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                            {{ __('messages.admin.offers.delete-text-1') }} <strong>{{ $offer->name }}</strong>{{ __('messages.admin.offers.delete-text-2') }}.
                                        </p>
                                        <div class="flex justify-end gap-4">
                                            <button @click="openDelete = false"
                                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                                {{ __('messages.button.cancel') }}
                                            </button>
                                            <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 cursor-pointer">
                                                    {{ __('messages.button.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </x-modal>

                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                {{ __('messages.admin.offers.no-offers') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-data="{ showOffer: false }" class="inline-block mt-5" x-cloak>
            <button @click="showOffer = true"
                class="flex  gap-2 items-center bg-themeBlue/80 border-2 border-themeBlbg-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded-lg transition cursor-pointer"><x-icon
                    name="plus" class="w-5 h-auto" /> {{ __('messages.button.create') }}</button>

            <x-modal :show="'showOffer'" @close="showOffer = false">
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.offers.create') }}</x-heading>
                <form action="{{ route('admin.job.offers.store') }}" method="POST"
                class="space-y-4 [&>select]:border-2  [&>select]:border-themeLightGray [&>input]:outline-0 [&>textarea]:outline-0">
                @csrf

                <x-label for="title">{{ __('messages.admin.offers.label-title') }}</x-label>
                <x-inputtext type="text" name="name" id="name" value="{{ old('name') }}" required />

                <x-label for="subtitle">{{ __('messages.admin.offers.label-subtitle') }}</x-label>
                <x-inputtext type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" required />

                <x-label for="description">{{ __('messages.admin.offers.label-description') }}</x-label>
                <x-textarea name="description" id="description" required>{{ old('description') }}</x-textarea>

                <x-label for="sector_category">{{ __('messages.admin.offers.label-sector') }}</x-label>
                <x-inputtext type="text" name="sector_category" id="sector_category" value="{{ old('sector_category') }}" required />

                <x-label for="general_category">{{ __('messages.admin.offers.label-category') }}</x-label>
                <x-inputtext type="text" name="general_category" id="general_category" value="{{ old('general_category') }}" required />

                <x-label for="state">{{ __('messages.admin.offers.label-state') }}</x-label>
                <select name="state" required
                    class="w-full border-themeLightGray rounded px-4 py-2 bg-white dark:bg-themeBgDark outline-0">
                    <option value="abierta" @selected(old('state') == 'abierta')>{{ __('messages.admin.offers.status-open') }}</option>
                    <option value="cerrada" @selected(old('state') == 'cerrada')>{{ __('messages.admin.offers.status-close') }}</option>
                </select>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="showOffer = false"
                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                        {{ __('messages.button.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                        {{ __('messages.button.save') }}
                    </button>
                </div>
            </form>
            </x-modal>

        </div>

    </div>
@endsection