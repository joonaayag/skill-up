{{-- Componente para mostrar un comentario individual y sus respuestas --}}

<div class="comment card mb-3" id="comment-{{ $comment->id }}">
    <div class="card-body">
        <div class="comment-meta">
            <strong>{{ $comment->user->name }}</strong> · {{ $comment->created_at->diffForHumans() }}
            @if($comment->created_at != $comment->updated_at)
                · <em>editado {{ $comment->updated_at->diffForHumans() }}</em>
            @endif
        </div>
        
        <div class="comment-content">
            {{ $comment->content }}
        </div>
        
        <div class="comment-actions mt-2">
            @auth
                <button class="btn btn-sm btn-link reply-btn" data-comment-id="{{ $comment->id }}">Responder</button>
                
                @if($comment->user_id == auth()->id())
                    <a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-link">Editar</a>
                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este comentario?')">Eliminar</button>
                    </form>
                @endif
            @endauth
        </div>
        
        @auth
            <div class="reply-form" id="reply-form-{{ $comment->id }}">
                <form action="{{ $type === 'project' ? route('projects.comments.store', $commentable->id) : route('school-projects.comments.store', $commentable->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="2" placeholder="Escribe tu respuesta..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Enviar respuesta</button>
                    <button type="button" class="btn btn-sm btn-secondary mt-2 reply-cancel" onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.remove('active')">Cancelar</button>
                </form>
            </div>
        @endauth
        
        {{-- Mostrar respuestas a este comentario --}}
        @if($comment->replies->count() > 0)
            <div class="comment-replies mt-3">
                @foreach($comment->replies as $reply)
                    <div class="comment card mb-2" id="comment-{{ $reply->id }}">
                        <div class="card-body">
                            <div class="comment-meta">
                                <strong>{{ $reply->user->name }}</strong> · {{ $reply->created_at->diffForHumans() }}
                                @if($reply->created_at != $reply->updated_at)
                                    · <em>editado {{ $reply->updated_at->diffForHumans() }}</em>
                                @endif
                            </div>
                            
                            <div class="comment-content">
                                {{ $reply->content }}
                            </div>
                            
                            <div class="comment-actions mt-2">
                                @auth
                                    @if($reply->user_id == auth()->id())
                                        <a href="{{ route('comments.edit', $reply->id) }}" class="btn btn-sm btn-link">Editar</a>
                                        <form action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este comentario?')">Eliminar</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>