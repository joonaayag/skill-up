@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <x-heading level="h1" class="mb-10">Proyectos Escolares</x-heading>

        <div class="overflow-x-auto rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Nombre</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Autor</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Categoría</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Tags</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Fecha</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Enlace</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schoolProjects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->title }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->author }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->general_category ?? '-' }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $project->tags ?? '-' }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                {{ $project->creation_date ? \Carbon\Carbon::parse($project->creation_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                @if ($project->link)
                                    <a href="{{ $project->link }}" target="_blank" class="text-themeBlue hover:underline">Ver</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                <a href="{{ route('admin.school_project.details', $project->id) }}"
                                    class="text-themeBlue hover:underline font-medium">Ver detalles</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                No hay proyectos escolares registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-data="{ showCreateSchoolProject: false }" class="inline-block mt-5">
            <button @click="showCreateSchoolProject = true"
                class="flex  gap-2 items-center bg-themeBlue/80 border-2 border-themeBlbg-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded-lg transition cursor-pointer"><x-icon
                    name="plus" class="w-5 h-auto" /> Crear</button>

            <x-modal :show="'showCreateSchoolProject'" @close="showCreateSchoolProject = false">
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Crear proyecto escolar</x-heading>
                <form method="POST" action="{{ route('admin.create.school.project') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-label for="title">Título:</x-label>
                        <x-inputtext type="text" name="title" id="title" required />
                    </div>

                    <div>
                        <x-label for="author">Author:</x-label>
                        <x-inputtext type="text" name="author" id="author" required />
                    </div>

                    <div>
                        <x-label for="title">Descripción:</x-label>
                        <x-textarea name="description" id="description" required></x-textarea>
                    </div>

                    <div>
                        <x-label for="title">Etiquetas (tags)</x-label>
                        <x-inputtext type="text" name="tags" id="tags" required />
                    </div>

                    <div>
                        <x-label for="title">Categoría general:</x-label>
                        <select name="general_category" required
                            class="w-full px-3 py-2 rounded border border-themeLightGray">
                            <option value="Administración y negocio">Administración y negocio</option>
                            <option value="Ciencia y salud">Ciencia y salud</option>
                            <option value="Comunicación">Comunicación</option>
                            <option value="Diseño y comunicación">Diseño y comunicación</option>
                            <option value="Educación">Educación</option>
                            <option value="Industria">Industria</option>
                            <option value="Otro">Otro</option>
                            <option value="Tecnología y desarrollo">Tecnología y desarrollo</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="title">Fecha de creación:</x-label>
                        <x-inputdate name="creation_date" id="creation_date" required />
                    </div>

                    <div>
                        <x-label for="title">Enlace (Opcional):</x-label>
                        <input type="url" name="link" class="w-full px-3 py-2 rounded border border-themeLightGray" />
                    </div>

                    <div>
                        <x-label for="title">Imagen destacada:</x-label>
                        <div x-data="{ fileName: '', previewUrl: '' }" class="w-full">
                            <label for="image-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                                🖼️ Subir imagen destacada
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
                        <x-label for="title">Archivos adicionales</x-label>
                        <div x-data="{ fileNames: [] }" class="w-full">
                            <label for="file-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                                📎 Subir archivos
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
                        <button type="submit"
                            class="px-4 py-2 bg-themeGrape/80 text-white rounded hover:bg-themeGrape transition cursor-pointer">
                            Guardar
                        </button>
                        <button type="button" @click="showCreateSchoolProject = false"
                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                            Cancelar
                        </button>
                    </div>

                </form>
            </x-modal>

        </div>


    </div>
@endsection