{{-- resources/views/comments/single_comment.blade.php --}}
<div class="comment bg-white dark:bg-themeDarkGray shadow-md rounded-lg p-5 mb-5" id="comment-{{ $comment->id }}">
    
    <div class="comment-meta text-sm text-gray-600 dark:text-gray-300 mb-2">
        <strong class="text-gray-800 dark:text-themeLightGray">{{ $comment->user->name . ' ' . $comment->user->last_name }}</strong>
        <span class="dark:text-themeLightGray">&nbsp;{{ $comment->created_at->diffForHumans() }}</span>
    </div>

    <div class="comment-content text-gray-800 dark:text-themeLightGray mb-4">
        {{ $comment->content }}
    </div>

    <div class="comment-actions flex flex-wrap gap-4 text-sm">
        @auth
            <button class="text-themeBlue hover:underline reply-btn cursor-pointer"
                data-comment-id="{{ $comment->id }}">{{ __('messages.answer') }}</button>

            @if($comment->user_id == auth()->id())
                <div x-cloak x-data="{ showModal: false }"
                    x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
                    class="relative z-10">
                    <button @click="showModal = true" class="text-red-600 hover:underline cursor-pointer">
                        {{ __('messages.button.delete') }}
                    </button>
                    
                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')

                        <x-modal>
                            <x-heading level="h2"
                                class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.heading-confirm') }}</x-heading>
                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" @click="showModal = false"
                                    class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                    {{ __('messages.button.cancel') }}
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-red-400 text-white rounded hover:bg-red-500 transition cursor-pointer">
                                    {{ __('messages.button.delete') }}
                                </button>
                            </div>
                        </x-modal>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    @auth
        <div class="reply-form mt-4 hidden" id="reply-form-{{ $comment->id }}">
            <div class="reply-feedback hidden mb-2" id="reply-feedback-{{ $comment->id }}">
                <div class="p-2 rounded text-sm">
                    <span class="feedback-message"></span>
                </div>
            </div>
            
            <form class="reply-form-element"
                action="{{ $type === 'project' ? route('projects.comments.store', $commentable->id) : route('school-projects.comments.store', $commentable->id) }}"
                method="POST"
                data-parent-id="{{ $comment->id }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="content" rows="2"
                    class="w-full p-2 outline-none border rounded-lg dark:bg-themeDark dark:text-white border-gray-300 dark:border-gray-600 resize-none focus:ring-0"
                    placeholder="Escribe tu respuesta..." required></textarea>
                <div class="flex gap-2 mt-2">
                    <button type="submit"
                        class="reply-submit-btn px-3 py-1 bg-themeBlue text-white text-sm rounded hover:bg-themeHoverBlue cursor-pointer">{{ __('messages.answer') }}</button>
                    <button type="button" class="px-3 py-1 bg-gray-300 dark:bg-gray-700 text-sm rounded reply-cancel cursor-pointer"
                        onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.add('hidden')">{{ __('messages.button.cancel') }}</button>
                </div>
            </form>
        </div>
    @endauth

    @if($comment->replies->count() > 0)
        <div class="comment-replies border-l-2 border-gray-200 dark:border-gray-600 pl-4 mt-4 space-y-4" id="replies-{{ $comment->id }}">
            @foreach($comment->replies as $reply)
                @include('comments.single_reply', ['reply' => $reply, 'type' => $type])
            @endforeach
        </div>
    @else
        <div class="comment-replies border-l-2 border-gray-200 dark:border-gray-600 pl-4 mt-4 space-y-4 hidden" id="replies-{{ $comment->id }}">
            {{-- Las nuevas respuestas se insertarán aquí --}}
        </div>
    @endif
</div>