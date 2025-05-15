@extends('layouts.app')

@section('title', 'Proyectos Escolares')

@section('content')
    <x-heading level="h1" class="mb-10">Gestión de proyectos escolares</x-heading>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-[250px_1fr] gap-20 p-4 dark:text-themeLightGray">
        <aside class="bg-white dark:bg-themeBgDark rounded-lg border-2 border-themeLightGray shadow px-4 py-5 space-y-4">
            <form id="filters" action="{{ route('school.projects.index') }}" method="get">
                <div class="flex flex-wrap gap-2 [&>input]:bg-white dark:[&>input]:bg-themeBgDark [&>select]:bg-white dark:[&>select]:bg-themeBgDark
                     [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray [&>select]:border-2 [&>select]:border-themeLightGray
                      [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0 mb-2">
                     <x-heading level="h3" class="mb-2.5">Filtros</x-heading>
                    <input type="text" name="title" placeholder="Buscar por título..." value="{{ request('title') }}"
                        class="input w-full py-2" />
                    <input type="text" name="author" placeholder="Buscar por autor..." value="{{ request('author') }}"
                        class="input w-full py-2" />
                    <select name="order" class="input w-full py-2">
                        <option>Ordenar por</option>
                        <option value="title" @selected(request('order') == 'title')>Título</option>
                        <option value="created_at" @selected(request('order') == 'created_at')>Fecha de creación</option>
                    </select>
                    <select name="direction" class="w-full py-2">
                        <option value="asc" @selected(request('direction') == 'asc')>Asc</option>
                        <option value="desc" @selected(request('direction') == 'desc')>Desc</option>
                    </select>
                </div>
                <div>
                    <x-heading level="h3" class="mb-2.5">Curso académico</x-heading>
                    <ul class="space-y-1">
                        <li><input type="checkbox" name="academic_year[]" value="2024-2025"
                                @checked(is_array(request('academic_year')) && in_array('2024-2025', request('academic_year'))) />
                            2024-2025</li>
                        <li><input type="checkbox" name="academic_year[]" value="2023-2024"
                                @checked(is_array(request('academic_year')) && in_array('2023-2024', request('academic_year'))) />
                            2023-2024</li>
                        <li><input type="checkbox" name="academic_year[]" value="2022-2023"
                                @checked(is_array(request('academic_year')) && in_array('2022-2023', request('academic_year'))) />
                            2022-2023</li>
                        <li><input type="checkbox" name="academic_year[]" value="2021-2022"
                                @checked(is_array(request('academic_year')) && in_array('2021-2022', request('academic_year'))) />
                            2021-2022</li>
                    </ul>
                </div>
                <div>
                    <x-heading level="h3" class="mb-2.5">Categoría</x-heading>
                    <ul class="space-y-1">
                        <li><input type="checkbox" name="tags[]" value="TFG" @checked(is_array(request('tags')) && in_array('TFG', request('tags'))) /> TFG</li>
                        <li><input type="checkbox" name="tags[]" value="TFM" @checked(is_array(request('tags')) && in_array('TFM', request('tags'))) /> TFM</li>
                        <li><input type="checkbox" name="tags[]" value="Tesis" @checked(is_array(request('tags')) && in_array('Tesis', request('tags'))) /> Tesis</li>
                        <li><input type="checkbox" name="tags[]" value="Individual" @checked(is_array(request('tags')) && in_array('Individual', request('tags'))) />
                            Individual</li>
                        <li><input type="checkbox" name="tags[]" value="Grupal" @checked(is_array(request('tags')) && in_array('Grupal', request('tags'))) />
                            Grupal</li>
                        <li><input type="checkbox" name="tags[]" value="Tecnología" @checked(is_array(request('tags')) && in_array('Tecnología', request('tags'))) />
                            Tecnología</li>
                        <li><input type="checkbox" name="tags[]" value="Ciencias" @checked(is_array(request('tags')) && in_array('Ciencias', request('tags'))) />
                            Ciencias</li>
                        <li><input type="checkbox" name="tags[]" value="Artes" @checked(is_array(request('tags')) && in_array('Artes', request('tags'))) />
                            Artes</li>
                        <li><input type="checkbox" name="tags[]" value="Ingeniería" @checked(is_array(request('tags')) && in_array('Ingeniería', request('tags'))) />
                            Ingeniería</li>
                    </ul>
                </div>
            
                
            </form>
        </aside>

        <main>
            
            <div class="bg-white border-2 border-themeLightGray dark:bg-themeBgDark rounded-lg shadow p-4">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Fecha de realización</th>
                            <th class="py-2">Fecha de publicación</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($projects->count())
                        @foreach($projects as $project)
                            <tr class="border-b">
                                <td class="py-2">
                                    <div class="font-semibold">{{ $project->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $project->author }}</div>
                                </td>
                                <td class="py-2">{{ $project->creation_date }}</td>
                                <td class="py-2">{{ $project->created_at }}</td>
                                <td class="py-2 space-x-2" x-data="{ showDelete: false, showEdit: false }" x-cloak x-init="
                                     $watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val));
                                     $watch('showEdit', val => document.body.classList.toggle('overflow-hidden', val));">

                                    <button @click="showDelete = true" 
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition cursor-pointer">
                                        Eliminar
                                    </button>

                                    <x-modal @click.outside="showDelete = false" :show="'showDelete'">
                                        <x-heading level="h2"
                                                    class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">
                                                    Seguro deseas eliminar este proyecto
                                                </x-heading>
                                                <form action="{{ route('school.projects.destroy', $project->id) }}"
                                                    method="POST" class="flex justify-center gap-3">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition cursor-pointer">
                                                        Sí, eliminar
                                                    </button>

                                                    <button type="button" @click="showDelete = false"
                                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition cursor-pointer">
                                                        Cancelar
                                                    </button>
                                                </form>
                                    </x-modal>

                                    <button @click="showEdit = true"  
                                        class="px-4 py-2 bg-themeBlue/80 hover:bg-themeBlue text-white rounded transition cursor-pointer">
                                        Editar
                                    </button>

                                    <x-modal @click.outside="showEdit = false" :show="'showEdit'">
                                                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">
                                                    Editar proyecto
                                                </x-heading>
                                                <form action="{{ route('school.projects.update', $project->id) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')

                                                    <x-label for="title">Titulo:</x-label>
                                                    <x-inputtext type="text" name="title" id="title"
                                                        value="{{ $project->title }}" required />

                                                    <x-label for="author">Autor:</x-label>
                                                    <x-inputtext type="text" name="author" id="author"
                                                        value="{{ $project->author }}" required />

                                                    <x-label for="date_created">Fecha de creacion:</x-label>
                                                    <input type="date" name="creation_date"
                                                        value="{{ $project->creation_date }}"
                                                        class="w-full mb-2 border px-2 py-1 rounded" required>

                                                    <x-label for="description">Descripción:</x-label>
                                                    <textarea name="description"
                                                        class="w-full mb-2 border px-2 py-1 rounded"
                                                        required>{{ $project->description }}</textarea>

                                                    <x-label for="tags">Tags:</x-label>
                                                    <x-inputtext type="text" name="tags" id="tags"
                                                        value="{{ $project->tags }}" required />

                                                    <x-label for="general_category">Categoria general:</x-label>
                                                    <x-inputtext type="text" name="general_category" id="general_category"
                                                        value="{{ $project->general_category }}" required />

                                                    <x-label for="url">Enlace (Opcional):</x-label>
                                                    <input type="url" name="link" value="{{ $project->link }}"
                                                        class="w-full mb-2 border px-2 py-1 rounded">

                                                    <x-label for="title">Imagen destacada:</x-label>
                                                    <div x-data="{ fileName: '', previewUrl: '' }" x-cloak class="w-full">
                                                        <label for="image-upload-{{ $project->id }}"
                                                            class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape/80 text-white font-medium rounded cursor-pointer hover:bg-themeGrape transition">
                                                            🖼️ Subir imagen destacada
                                                            <input id="image-upload-{{ $project->id }}" type="file"
                                                                name="image" accept="image/*" class="hidden" @change="
                                                                fileName = $event.target.files[0]?.name || '';
                                                                if ($event.target.files[0]) {
                                                                 const reader = new FileReader();
                                                                 reader.onload = e => previewUrl = e.target.result;
                                                                 reader.readAsDataURL($event.target.files[0]);
                                                                 }" />
                                                        </label>

                                                        <template x-if="fileName">
                                                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄
                                                                <span x-text="fileName"></span></p>
                                                        </template>

                                                        <template x-if="previewUrl">
                                                            <img :src="previewUrl" alt="Vista previa"
                                                                class="mt-3 max-h-48 rounded border border-gray-300 shadow" />
                                                        </template>
                                                    </div>

                                                    <x-label for="title">Archivos adicionales</x-label>
                                                    <div x-data="{ fileNames: [] }" x-cloak class="w-full">
                                                        <label for="file-upload-{{ $project->id }}"
                                                            class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape/80 text-white font-medium rounded cursor-pointer hover:bg-themeGrape transition">
                                                            📎 Subir archivos
                                                            <input id="file-upload-{{ $project->id }}" name="files[]"
                                                                type="file" multiple accept="file/*" class="hidden"
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

                                                    <div class="flex justify-end gap-3 mt-4">
                                                        <button type="button" @click="showEdit = false"
                                                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit"
                                                            class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                                                            Guardar
                                                        </button>
                                                    </div>
                                    </x-modal>
                                </td>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="py-4 text-center">No hay proyectos disponibles</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div x-data="{ showModal: false }" x-cloak
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition cursor-pointer" >
            <x-icon name="plus" />
        </button>


        <x-modal>
            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Nuevo proyecto</x-heading>

            <form action="{{ route('school.projects.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
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
                    <select name="general_category" required class="w-full px-3 py-2 rounded border border-themeLightGray">
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
                    <div x-data="{ fileName: '', previewUrl: '' }" x-cloak class="w-full">
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
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄 <span x-text="fileName"></span></p>
                        </template>

                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Vista previa"
                                class="mt-3 max-h-48 rounded border border-gray-300 shadow" />
                        </template>
                    </div>

                </div>

                <div>
                    <x-label for="title">Archivos adicionales</x-label>
                    <div x-data="{ fileNames: [] }" x-cloak class="w-full">
                        <label for="file-upload"
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                            📎 Subir archivos
                            <input id="file-upload" name="files[]" type="file" multiple accept="file/*" class="hidden"
                                @change="fileNames = [...$event.target.files].map(f => f.name)" />
                        </label>

                        <template x-if="fileNames.length > 0">
                            <ul class="mt-2 text-sm text-black dark:text-themeLightGray space-y-1 list-disc list-inside">
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
                    <button type="button" @click="showModal = false"
                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filters');

            if (form) {
                const inputs = form.querySelectorAll('input, select');

                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        form.submit();
                    });

                    if (input.tagName === 'INPUT') {
                        input.addEventListener('keyup', () => {
                            clearTimeout(input._timeout);
                            input._timeout = setTimeout(() => form.submit(), 1000);
                        });
                    }
                });
            }
        });
    </script>

@endsection