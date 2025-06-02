{{-- resources/views/comments/single_reply.blade.php --}}
<div class="reply bg-gray-50 dark:bg-themeSmallTextLightGray p-4 rounded-lg new-reply" id="comment-{{ $reply->id }}">
    <div class="comment-meta text-sm text-dark dark:text-gray-300 mb-1">
        <strong class="text-black dark:text-themeLightGray">{{ $reply->user->name . ' ' . $reply->user->last_name }}</strong> 
        <span class="text-themeDarkGray dark:text-themeLightGray"> {{ $reply->created_at->diffForHumans() }}</span>
    </div>
    <div class="comment-content text-gray-800 dark:text-themeLightGray mb-2">
        {{ $reply->content }}
    </div>
    <div class="comment-actions text-sm flex gap-3">
        @auth
            @if($reply->user_id == auth()->id())
                <div x-cloak x-data="{ showModal: false }"
                    x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
                    class="relative z-10">
                    <button @click="showModal = true" class="text-red-600 hover:underline cursor-pointer">
                        {{ __('messages.button.delete') }}
                    </button>
                    
                    <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')

                        <x-modal>
                            <x-heading level="h2"
                                class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.heading-confirm')}}</x-heading>
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
</div>