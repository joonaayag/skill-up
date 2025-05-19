{{-- Uso: @include('comments.comment-section', ['commentable' => $project, 'type' => 'project']) --}}
{{-- O: @include('comments.comment-section', ['commentable' => $schoolProject, 'type' => 'school-project']) --}}

<div class="comment_section mt-10 max-w-4xl ">
    <h3 class="text-2xl font-semibold mb-6 text-black dark:text-themeLightGray">
        {{ __('messages.project-details.comments')  }} ({{ $commentable->comments->count() }})
    </h3>

    @auth
        <div class="bg-white dark:bg-themeDarkGray rounded-lg shadow-md p-6 mb-8">
            @if ($errors->any())
                <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="text-black dark:text-white">- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h4 class="text-lg font-semibold mb-3 text-gray-700 dark:text-themeLightGray">{{ __('messages.project-details.add-comment')  }}</h4>
            <form
                action="{{ $type === 'project' ? route('projects.comments.store', $commentable->id) : route('school-projects.comments.store', $commentable->id) }}"
                method="POST">
                @csrf
                <textarea name="content" rows="2"
                    class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-800 focus:ring-2 focus:ring-themeBlue focus:outline-none dark:bg-themeDark focus:text-white"
                    placeholder="{{ __('messages.project-details.write-comment')  }}" required></textarea>
                <button type="submit"
                    class="mt-4 px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeHoverBlue transition">
                    {{ __('messages.project-details.publish-comment')  }}
                </button>
            </form>
        </div>
    @endauth

    <div class="comments-list space-y-6">
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
    document.addEventListener('DOMContentLoaded', function () {
        // Mostrar/ocultar formulario de respuesta al hacer clic en "Responder"
        document.querySelectorAll('.reply-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
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