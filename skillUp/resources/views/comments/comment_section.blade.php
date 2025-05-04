{{-- Uso: @include('comments.comment-section', ['commentable' => $project, 'type' => 'project']) --}}
{{-- O: @include('comments.comment-section', ['commentable' => $schoolProject, 'type' => 'school-project']) --}}

<div class="comment_section mt-5">
    <h3 class="mb-4">Comentarios ({{ $commentable->comments->count() }})</h3>
    
    @auth
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Añadir comentario</h5>
                <form action="{{ $type === 'project' ? route('projects.comments.store', $commentable->id) : route('school-projects.comments.store', $commentable->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="Escribe tu comentario aquí..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Publicar comentario</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <a href="{{ route('login') }}">Inicia sesión</a> para dejar un comentario.
        </div>
    @endauth
    
    <div class="comments-list">
        @foreach($commentable->mainComments()->orderBy('created_at', 'desc')->get() as $comment)
            @include('comments.single_comment', ['comment' => $comment, 'type' => $type, 'commentable' => $commentable])
        @endforeach
    </div>
</div>

{{-- Estilos para comentarios anidados --}}
<style>
    .comments-list {
        margin-bottom: 30px;
    }
    .comment {
        margin-bottom: 15px;
    }
    .comment-replies {
        margin-left: 40px;
        border-left: 1px solid #ddd;
        padding-left: 20px;
    }
    .comment-meta {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 5px;
    }
    .comment-actions {
        font-size: 0.85rem;
    }
    .reply-form {
        margin-top: 10px;
        margin-bottom: 15px;
        display: none;
    }
    .reply-form.active {
        display: block;
    }
</style>

{{-- Scripts para manejar la lógica de respuestas --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar formulario de respuesta al hacer clic en "Responder"
        document.querySelectorAll('.reply-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const commentId = this.getAttribute('data-comment-id');
                const replyForm = document.getElementById('reply-form-' + commentId);
                
                if (replyForm) {
                    replyForm.classList.toggle('active');
                    if (replyForm.classList.contains('active')) {
                        replyForm.querySelector('textarea').focus();
                    }
                }
            });
        });
    });
</script>