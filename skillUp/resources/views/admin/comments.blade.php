@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">{{ __('messages.admin.comments.title') }}</h1>
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
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.comments.table-name') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.comments.table-project') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.comments.table-school-project') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.comments.table-content') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.comments.table-date') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.comments.table-actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->user->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->project->title ?? __('messages.admin.comments.no-projects') }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->project->school_project->title ?? __('messages.admin.comments.no-school-project') }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->content }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->created_at }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700 whitespace-nowrap space-x-3">

                                <div x-data="{ openEdit: false }" class="inline-block" x-cloak>
                                    <button @click="openEdit = true"
                                        class="bg-themeBlue/80 border-2 border-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.edit') }}</button>

                                    <x-modal :show="'openEdit'" @click.outside="openEdit = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.comments.edit') }}</x-heading>

                                        <form action="{{ route('admin.comments.update', $comment->id) }}" method="POST" id="edit-comment-form"
                                            class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <div id="comment-errors" class="bg-red-100 text-red-700 p-4 rounded hidden">
                                                <ul class="list-disc list-inside text-sm"></ul>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-4">{{ __('messages.admin.comments.content') }}</label>
                                                <input type="text" name="content" value="{{ $comment->content }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                    required>
                                            </div>

                                            <div class="mt-6 flex justify-end gap-4">
                                                <button type="button" @click="openEdit = false"
                                                    class="h-8 sm:h-10 px-3 text-xs md:tex-sm lg:text-base bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                    {{ __('messages.button.cancel') }}
                                                </button>
                                                <button type="submit"
                                                    class="bg-blue-600 text-white h-8 sm:h-10 px-3 text-xs md:tex-sm lg:text-base rounded hover:bg-blue-700 cursor-pointer">
                                                    {{ __('messages.button.save-changes') }}
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </div>

                                <div x-data="{ openDelete: false }" class="inline-block" x-cloak>
                                    <button @click="openDelete = true"
                                        class="bg-red-500 border-2 border-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.delete') }}</button>

                                    <x-modal :show="'openDelete'" @click.outside="openDelete = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.comments.delete-confirm') }}</x-heading>
                                        <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                            {{ __('messages.admin.comments.delete-text-1') }}  <strong>{{ $comment->user->name }}: {{ $comment->content }}</strong>{{ __('messages.admin.comments.delete-text-2') }}
                                        </p>
                                        <div class="flex justify-end gap-4">
                                            <button @click="openDelete = false"
                                                class="h-8 sm:h-10 px-3 text-xs md:tex-sm lg:text-base bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                                {{ __('messages.button.cancel') }}
                                            </button>
                                            <form action="{{ route('admin.comment.destroy', $comment->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="h-8 sm:h-10 px-3 text-xs md:tex-sm lg:text-base bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer">
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
                                {{ __('messages.admin.comments.no-comments') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    <script>
        document.getElementById('edit-comment-form').addEventListener('submit', function (event) {
            const form = event.target;
            const contentInput = form.querySelector('input[name="content"]');
            const content = contentInput.value.trim();
            const errors = [];

            // Validaciones
            if (!content) {
                errors.push("{{ __('messages.errors.comment.required') }}");
            } else if (content.length > 100) {
                errors.push("{{ __('messages.errors.comment.max') }}");
            }

            // Mostrar errores
            const errorBox = document.getElementById('comment-errors');
            const errorList = errorBox.querySelector('ul');
            errorList.innerHTML = '';

            if (errors.length > 0) {
                event.preventDefault();
                errorBox.classList.remove('hidden');
                errors.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    errorList.appendChild(li);
                });
                window.scrollTo({ top: errorBox.offsetTop - 20, behavior: 'smooth' });
            } else {
                errorBox.classList.add('hidden');
            }
        });
    </script>
@endsection