@extends('layouts.app')

@section('title', __('messages.titles.admin-school-projects'))

@section('content')
    <div class="container mx-auto px-6 py-10">
        <x-heading level="h1" class="mb-10">{{ __('messages.admin.school-projects.title') }}</x-heading>
        @if ($errors->any())
            <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-black dark:text-white">- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.table-name') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.table-author') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.table-category') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.table-tags') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.table-date') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.table-link') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">
                            {{ __('messages.admin.school-projects.see-details') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schoolProjects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->title }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->author }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                            @php
                                $categoryMap = [
                                    'Administración y negocio' => 'option-admin',
                                    'Ciencia y salud' => 'option-science',
                                    'Comunicación' => 'option-comunication',
                                    'Diseño y comunicación' => 'option-design',
                                    'Educación' => 'option-education',
                                    'Industria' => 'option-industry',
                                    'Otro' => 'option-other',
                                    'Tecnología y desarrollo' => 'option-tec',
                                ];

                                $categoryKey = $categoryMap[$project->general_category] ?? null;
                            @endphp

                                @if ($categoryKey)
                                        {{ __('messages.projects.' . $categoryKey) }}
                                @endif
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                {{ __('messages.tags.' . strtolower($project->tags)) ?? '-' }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                {{ $project->creation_date ? \Carbon\Carbon::parse($project->creation_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                @if ($project->link)
                                    <a href="{{ $project->link }}" target="_blank"
                                        class="text-themeBlue hover:underline">{{ __('messages.admin.school-projects.see') }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                <a href="{{ route('admin.school_project.details', $project->id) }}"
                                    class="text-themeBlue hover:underline font-medium">{{ __('messages.admin.school-projects.see-details') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                {{ __('messages.admin.school-projects.no-projects') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-data="{ showCreateSchoolProject: false }" class="inline-block mt-5" x-cloak>
            <button @click="showCreateSchoolProject = true"
                class="flex  gap-2 items-center bg-themeBlue/80 border-2 border-themeBlbg-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded-lg transition cursor-pointer"><x-icon
                    name="plus" class="w-5 h-auto" /> {{ __('messages.button.create') }}</button>

            <x-modal :show="'showCreateSchoolProject'" @close="showCreateSchoolProject = false">
                <x-heading level="h2"
                    class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.school-projects.create-project') }}</x-heading>
                <form method="POST" action="{{ route('admin.create.school.project') }}" class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                    @csrf

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-title') }}</x-label>
                        <x-inputtext type="text" name="title" id="title" required />
                    </div>

                    <div>
                        <x-label for="author">{{ __('messages.school-projects.label-author') }}</x-label>
                        <x-inputtext type="text" name="author" id="author" required />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-description') }}</x-label>
                        <x-textarea name="description" id="description" required></x-textarea>
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-tags') }}</x-label>
                        <select name="tags" id="tags"
                            class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray" required>
                            <option value="TFG" {{ old('tags') == 'TFG' ? 'selected' : '' }}>
                                {{ __('messages.tags.tfg') }}
                            </option>
                            <option value="TFM" {{ old('tags') == 'TFM' ? 'selected' : '' }}>
                                {{ __('messages.tags.tfm') }}
                            </option>
                            <option value="Tesis" {{ old('tags') == 'Tesis' ? 'selected' : '' }}>
                                {{ __('messages.tags.tesis') }}
                            </option>
                            <option value="Individual" {{ old('tags') == 'Individual' ? 'selected' : '' }}>
                                {{ __('messages.tags.individual') }}
                            </option>
                            <option value="Grupal" {{ old('tags') == 'Grupal' ? 'selected' : '' }}>
                                {{ __('messages.tags.grupal') }}
                            </option>
                            <option value="Tecnología" {{ old('tags') == 'Tecnología' ? 'selected' : '' }}>
                                {{ __('messages.tags.tecnología') }}
                            </option>
                            <option value="Ciencias" {{ old('tags') == 'Ciencias' ? 'selected' : '' }}>
                                {{ __('messages.tags.ciencias') }}
                            </option>
                            <option value="Artes" {{ old('tags') == 'Artes' ? 'selected' : '' }}>
                                {{ __('messages.tags.artes') }}
                            </option>
                            <option value="Ingeniería" {{ old('tags') == 'Ingeniería' ? 'selected' : '' }}>
                                {{ __('messages.tags.ingenieria') }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-category') }}</x-label>
                        <select name="general_category" required
                            class="w-full px-3 py-2 rounded border border-themeLightGray dark:bg-themeBgDark">
                            <option value="Administración y negocio">{{ __('messages.school-projects.option-admin') }}
                            </option>
                            <option value="Ciencia y salud">{{ __('messages.school-projects.option-science') }}</option>
                            <option value="Comunicación">{{ __('messages.school-projects.option-comunication') }}</option>
                            <option value="Diseño y comunicación">{{ __('messages.school-projects.option-design') }}
                            </option>
                            <option value="Educación">{{ __('messages.school-projects.option-education') }}</option>
                            <option value="Industria">{{ __('messages.school-projects.option-industry') }}</option>
                            <option value="Otro">{{ __('messages.school-projects.option-other') }}</option>
                            <option value="Tecnología y desarrollo">{{ __('messages.school-projects.option-tec') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.table-dateC') }}</x-label>
                        <x-inputdate name="creation_date" id="creation_date" required />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-link') }}</x-label>
                        <input type="url" name="link" class="w-full px-3 py-2 rounded border border-themeLightGray" />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-highlight') }}</x-label>
                        <div x-data="{ fileName: '', previewUrl: '' }" x-cloak class="w-full">
                            <label for="image-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                                <div class="flex gap-2">
                                    <x-icon name="image" class="w-5 h-auto" />
                                    {{ __('messages.projects.label-highlight') }}
                                </div>
                                <input id="image-upload" type="file" name="image" accept="image/*" class="hidden" @change="
                                                fileName = $event.target.files[0]?.name || '';
                                                if ($event.target.files[0]) {
                                                const reader = new FileReader();
                                                reader.onload = e => previewUrl = e.target.result;
                                                reader.readAsDataURL($event.target.files[0]);
                                                }" />
                            </label>

                            <template x-if="fileName">
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄 <span x-text="fileName"></span>
                                </p>
                            </template>

                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Vista previa"
                                    class="mt-3 max-h-48 rounded border border-gray-300 shadow" />
                            </template>
                        </div>

                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.school-projects.label-additional') }}</x-label>
                        <div x-data="{ fileNames: [] }" x-cloak class="w-full">
                            <label for="file-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                                <div class="flex gap-2">
                                    <x-icon name="clip" class="w-5 h-auto" />
                                    {{ __('messages.projects.label-upload') }}
                                </div> 
                                <input id="file-upload" name="files[]" type="file" multiple accept="file/*" class="hidden"
                                    @change="fileNames = [...$event.target.files].map(f => f.name)" />
                            </label>

                            <template x-if="fileNames.length > 0">
                                <ul
                                    class="mt-2 text-sm text-black dark:text-themeLightGray space-y-1 list-disc list-inside">
                                    <template x-for="name in fileNames" :key="name">
                                        <li x-text="name"></li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="showCreateSchoolProject = false"
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