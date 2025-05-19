<div class="comment bg-white dark:bg-themeDarkGray shadow-md rounded-lg p-5 mb-5" id="comment-{{ $comment->id }}">
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="comment-meta text-sm text-gray-600 dark:text-gray-300 mb-2">
        <strong
            class="text-gray-800 dark:text-themeLightGray ">{{ $comment->user->name . ' ' . $comment->user->last_name  }}</strong>
        <span class="dark:text-themeLightGray">&nbsp;{{  $comment->created_at->diffForHumans() }}</span>
        <!-- @if($comment->created_at != $comment->updated_at)
            · <em class="dark:text-themeLightGray">editado {{ $comment->updated_at->diffForHumans() }}</em>
        @endif -->
    </div>

    <div class="comment-content text-gray-800 dark:text-themeLightGray mb-4">
        {{ $comment->content }}
    </div>

    <div class="comment-actions flex flex-wrap gap-4 text-sm">
        @auth
            <button class="text-themeBlue hover:underline reply-btn" data-comment-id="{{ $comment->id }}">Responder</button>

            @if($comment->user_id == auth()->id())
                <!-- <a href="{{ route('comments.edit', $comment->id) }}" class="text-yellow-600 hover:underline">Editar</a> -->
                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline"
                        onclick="return confirm('¿Estás seguro de que deseas eliminar este comentario?')">Eliminar</button>
                </form>
            @endif
        @endauth
    </div>

    @auth
        <div class="reply-form mt-4 hidden" id="reply-form-{{ $comment->id }}">
            <form
                action="{{ $type === 'project' ? route('projects.comments.store', $commentable->id) : route('school-projects.comments.store', $commentable->id) }}"
                method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="content" rows="2"
                    class="w-full p-2 border rounded-lg dark:bg-themeDark dark:text-white border-gray-300 dark:border-gray-600"
                    placeholder="Escribe tu respuesta..." required></textarea>
                <div class="flex gap-2 mt-2">
                    <button type="submit"
                        class="px-3 py-1 bg-themeBlue text-white text-sm rounded hover:bg-themeHoverBlue">Enviar
                        respuesta</button>
                    <button type="button" class="px-3 py-1 bg-gray-300 dark:bg-gray-700 text-sm rounded reply-cancel"
                        onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.add('hidden')">Cancelar</button>
                </div>
            </form>
        </div>
    @endauth

    @if($comment->replies->count() > 0)
        <div class="comment-replies border-l-2 border-gray-200 dark:border-gray-600 pl-4 mt-4 space-y-4">
            @foreach($comment->replies as $reply)
                <div class="reply bg-gray-50 dark:bg-themeSmallTextLightGray p-4 rounded-lg" id="comment-{{ $reply->id }}">
                    <div class="comment-meta text-sm text-dark dark:text-gray-300 mb-1">
                        <strong class="text-black dark:text-themeLightGray">{{ $reply->user->name }}</strong> <span
                            class="text-themeDarkGray dark:text-themeLightGray"> {{ $reply->created_at->diffForHumans() }}
                        </span>
                        <!-- @if($reply->created_at != $reply->updated_at)
                                    · <em class="dark:text-themeLightGray">editado {{ $reply->updated_at->diffForHumans() }}</em>
                                @endif -->
                    </div>
                    <div class="comment-content text-gray-800 dark:text-themeLightGray mb-2">
                        {{ $reply->content }}
                    </div>
                    <div class="comment-actions text-sm flex gap-3">
                        @auth
                            @if($reply->user_id == auth()->id())
                                <!-- <a href="{{ route('comments.edit', $reply->id) }}" class="text-yellow-600 hover:underline">Editar</a> -->
                                <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este comentario?')">Eliminar</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>