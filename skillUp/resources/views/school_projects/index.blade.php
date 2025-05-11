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
            <div>
                <x-heading level="h3" class="mb-2.5">Curso académico</x-heading>
                <ul class="space-y-1">
                    <li><input type="checkbox" /> 2024-2025</li>
                    <li><input type="checkbox" /> 2023-2024</li>
                    <li><input type="checkbox" /> 2022-2023</li>
                    <li><input type="checkbox" /> 2021-2022</li>
                </ul>
            </div>
            <div>
                <x-heading level="h3" class="mb-2.5">Categoría</x-heading>
                <ul class="space-y-1">
                    <li><input type="checkbox" /> TFG</li>
                    <li><input type="checkbox" /> TFM</li>
                    <li><input type="checkbox" /> Tesis</li>
                    <li><input type="checkbox" /> Individual</li>
                    <li><input type="checkbox" /> Grupal</li>
                    <li><input type="checkbox" /> Tecnología</li>
                    <li><input type="checkbox" /> Ciencias</li>
                    <li><input type="checkbox" /> Artes</li>
                    <li><input type="checkbox" /> Ingeniería</li>
                </ul>
            </div>
        </aside>

        <main class="space-y-4">
            <div
                class="flex flex-wrap gap-2 h-12 [&>input]:h-full [&>select]:h-full [&>input]:bg-white dark:[&>input]:bg-themeBgDark [&>select]:bg-white dark:[&>select]:bg-themeBgDark
                 [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0">
                <input type="text" placeholder="Buscar por título..." class="input" />
                <input type="text" placeholder="Buscar por autor..." class="input" />
                <select class="input">
                    <option>Ordenar por</option>
                    <option value="name" @selected(request('order') == 'name')>Título</option>
                    <option value="puesto" @selected(request('order') == 'puesto')>Puesto</option>
                    <option value="created_at" @selected(request('order') == 'created_at')>Fecha de creación</option>
                </select>
            </div>

            <div class="bg-white dark:bg-themeBgDark rounded shadow p-4">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Fecha de realización</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr class="border-b">
                                <td class="py-2">
                                    <div class="font-semibold">{{ $project->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $project->author }}</div>
                                </td>
                                <td class="py-2">{{ $project->creation_date }}</td>
                                <td class="py-2 space-x-2 relative z-10" x-data="{ showDelete: false, showEdit: false }" x-init="
                                 $watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val));
                                 $watch('showEdit', val => document.body.classList.toggle('overflow-hidden', val));
                                                                    " class="relative z-10">

                                    <button @click="showDelete = true"
                                        class="p-2 bg-themeBlue text-white shadow-lg rounded-lg hover:bg-themeHoverBlue transition">
                                        Eliminar
                                    </button>

                                    <x-modal :show="'showDelete'">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Seguro
                                            deses eliminar esta mierda</x-heading>
                                        <form action="{{ route('school.projects.destroy', $project->id) }}" method="POST"
                                            class="flex justify-center gap-3">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                                Sí, eliminar
                                            </button>

                                            <button type="button" @click="showDelete = false"
                                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                                                Cancelar
                                            </button>
                                        </form>

                                    </x-modal>

                                    <button @click="showEdit = true"
                                        class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1 rounded"
                                        @click="showEdit = true">
                                        Editar
                                    </button>

                                    <x-modal :show="'showEdit'">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Seguro
                                            deses eliminar esta mierda</x-heading>
                                        <form action="{{ route('school.projects.update', $project->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <x-label for="title">Titulo:</x-label>
                                            <x-inputtext type="text" name="title" id="title" value="{{ $project->title }}" required />

                                            <x-label for="author">Autor:</x-label>
                                            <x-inputtext type="text" name="author" id="author" value="{{ $project->author }}" required />

                                            <x-label for="date_created">Fecha de creacion:</x-label>
                                            <input type="date" name="creation_date" value="{{ $project->creation_date }}"
                                                class="w-full mb-2 border px-2 py-1 rounded" required>

                                            <x-label for="description">Descripción:</x-label>
                                            <textarea name="description" class="w-full mb-2 border px-2 py-1 rounded"
                                                required>{{ $project->description }}</textarea>

                                            <x-label for="tags">Tags:</x-label>
                                            <x-inputtext type="text" name="tags" id="tags" value="{{ $project->tags }}" required />

                                            <x-label for="general_category">Categoria general:</x-label>
                                            <x-inputtext type="text" name="general_category" id="general_category" value="{{ $project->general_category }}" required />

                                            <x-label for="url">Enlace (Opcional):</x-label>
                                            <input type="url" name="link" value="{{ $project->link }}"
                                                class="w-full mb-2 border px-2 py-1 rounded">

                                            <x-label for="title">Imagen destacada:</x-label>
                                            <div x-data="{ fileName: '', previewUrl: '' }" class="w-full">
                                                <label for="image-upload"
                                                    class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                                                    🖼️ Subir imagen destacada
                                                    <input id="image-upload" type="file" name="image" accept="image/*"
                                                        class="hidden" @change="
                                                    fileName = $event.target.files[0]?.name || '';
                                                    if ($event.target.files[0]) {
                                                     const reader = new FileReader();
                                                     reader.onload = e => previewUrl = e.target.result;
                                                     reader.readAsDataURL($event.target.files[0]);
                                                     }" />
                                                </label>

                                                <template x-if="fileName">
                                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄 <span
                                                            x-text="fileName"></span></p>
                                                </template>

                                                <template x-if="previewUrl">
                                                    <img :src="previewUrl" alt="Vista previa"
                                                        class="mt-3 max-h-48 rounded border border-gray-300 shadow" />
                                                </template>
                                            </div>

                                            <x-label for="title">Archivos adicionales</x-label>
                                            <div x-data="{ fileNames: [] }" class="w-full">
                                                <label for="file-upload"
                                                    class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                                                    📎 Subir archivos
                                                    <input id="file-upload" name="files[]" type="file" multiple accept="file/*"
                                                        class="hidden"
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

                                            <button type="submit"
                                                class="bg-teal-600 text-white px-3 py-1 rounded">Guardar</button>
                                            <button type="button" @click="showEdit = false"
                                                class="ml-2 px-3 py-1 border rounded">Cancelar</button>
                                        </form>

                                    </x-modal>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition">
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
                        <option value="Tecnología y desarrollo">Tecnología y desarrollo</option>
                        <option value="Diseño y comunicación">Diseño y comunicación</option>
                        <option value="Administración y negocio">Administración y negocio</option>
                        <option value="Comunicación">Comunicación</option>
                        <option value="Educación">Educación</option>
                        <option value="Ciencia y salud">Ciencia y salud</option>
                        <option value="Industria">Industria</option>
                        <option value="Otro">Otro</option>
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
                    <div x-data="{ fileNames: [] }" class="w-full">
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
            </form>
        </x-modal>
    </div>
@endsection