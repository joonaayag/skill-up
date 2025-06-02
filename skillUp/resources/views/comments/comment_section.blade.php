{{-- Uso: @include('comments.comment-section', ['commentable' => $project, 'type' => 'project']) --}}
{{-- O: @include('comments.comment-section', ['commentable' => $schoolProject, 'type' => 'school-project']) --}}

<div class="comment_section mt-10 max-w-4xl ">
    <h3 class="text-2xl font-semibold mb-6 text-black dark:text-themeLightGray">
        {{ __('messages.project-details.comments')  }} (<span id="comments-count">{{ $commentable->comments->count() }}</span>)
    </h3>

    @auth
        <div class="bg-white dark:bg-themeDarkGray rounded-lg shadow-md p-6 mb-8">
            <h4 class="text-lg font-semibold mb-3 text-gray-700 dark:text-themeLightGray">
                {{ __('messages.project-details.add-comment')  }}
            </h4>
            <form id="commentForm"
                action="{{ $type === 'project' ? route('projects.comments.store', $commentable->id) : route('school-projects.comments.store', $commentable->id) }}"
                method="POST">
                @csrf
                <textarea name="content" rows="2"
                    class="w-full p-3 outline-none rounded-lg resize-none border text-black dark:text-themeLightGray border-gray-300 dark:border-gray-800 focus:outline-none dark:bg-themeDark"
                    placeholder="{{ __('messages.project-details.write-comment') }}" required></textarea>
                <button type="submit" id="submit-comment-btn"
                    class="mt-4 px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-themeBlue text-white rounded hover:bg-themeHoverBlue transition cursor-pointer">
                    {{ __('messages.project-details.publish-comment') }}
                </button>
            </form>
            
            <!-- Mensaje de feedback -->
            <div id="comment-feedback" class="mt-3 hidden">
                <div class="p-3 rounded-lg">
                    <span id="feedback-message"></span>
                </div>
            </div>
        </div>
    @endauth

    <div id="commentsList" class="comments-list space-y-6">
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

    /* Estilos para el feedback */
    .feedback-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .feedback-error {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    /* Animación para nuevas respuestas */
    .new-reply {
        animation: fadeInSlide 0.5s ease-in-out;
    }

    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

{{-- Scripts para manejar la lógica de respuestas y AJAX --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Variables del DOM
        const commentForm = document.getElementById('commentForm');
        const submitBtn = document.getElementById('submit-comment-btn');
        const feedbackDiv = document.getElementById('comment-feedback');
        const feedbackMessage = document.getElementById('feedback-message');
        const commentsList = document.getElementById('commentsList');
        const commentsCount = document.getElementById('comments-count');

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

        // Función para mostrar feedback
        function showFeedback(message, type = 'success') {
            feedbackMessage.textContent = message;
            feedbackDiv.className = `mt-3 feedback-${type}`;
            feedbackDiv.classList.remove('hidden');
            
            // Ocultar después de 3 segundos
            setTimeout(() => {
                feedbackDiv.classList.add('hidden');
            }, 3000);
        }

        // Función para deshabilitar/habilitar el botón
        function toggleSubmitButton(disabled = false) {
            submitBtn.disabled = disabled;
            if (disabled) {
                submitBtn.textContent = 'Enviando...';
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.textContent = '{{ __("messages.project-details.publish-comment") }}';
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Manejar envío del formulario principal de comentarios
        if (commentForm) {
            commentForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const url = this.action;
                const token = this.querySelector('input[name="_token"]').value;

                toggleSubmitButton(true);
                feedbackDiv.classList.add('hidden');

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Limpiar el formulario
                        this.reset();
                        
                        // Mostrar mensaje de éxito
                        showFeedback(data.message, 'success');
                        
                        // Insertar el nuevo comentario al principio de la lista
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.comment_html;
                        const newComment = tempDiv.firstElementChild;
                        newComment.classList.add('new-comment');
                        
                        commentsList.insertBefore(newComment, commentsList.firstChild);
                        
                        // Actualizar contador de comentarios
                        if (commentsCount && data.comments_count) {
                            commentsCount.textContent = data.comments_count;
                        }
                        
                    } else {
                        throw new Error(data.message || 'Error al enviar el comentario');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    showFeedback(error.message || 'Ha ocurrido un error inesperado', 'error');
                } finally {
                    toggleSubmitButton(false);
                }
            });
        }

        // Función para mostrar feedback en respuestas
        function showReplyFeedback(parentId, message, type = 'success') {
            const feedbackDiv = document.getElementById(`reply-feedback-${parentId}`);
            const feedbackMessage = feedbackDiv.querySelector('.feedback-message');
            
            feedbackMessage.textContent = message;
            feedbackDiv.className = `reply-feedback mb-2 feedback-${type}`;
            feedbackDiv.classList.remove('hidden');
            
            // Ocultar después de 3 segundos
            setTimeout(() => {
                feedbackDiv.classList.add('hidden');
            }, 3000);
        }

        // Manejar envío de respuestas (comentarios anidados)
        document.addEventListener('submit', async function(e) {
            if (e.target.classList.contains('reply-form-element')) {
                e.preventDefault();
                
                const form = e.target;
                const formData = new FormData(form);
                const url = form.action;
                const token = form.querySelector('input[name="_token"]').value;
                const submitButton = form.querySelector('.reply-submit-btn');
                const parentId = form.dataset.parentId;
                const replyFeedback = document.getElementById(`reply-feedback-${parentId}`);
                const repliesContainer = document.getElementById(`replies-${parentId}`);
                
                // Deshabilitar botón y mostrar estado de carga
                submitButton.disabled = true;
                submitButton.textContent = 'Enviando...';
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                
                // Ocultar feedback previo
                if (replyFeedback) {
                    replyFeedback.classList.add('hidden');
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Limpiar formulario de respuesta
                        form.reset();
                        
                        // Ocultar formulario de respuesta
                        const replyForm = document.getElementById(`reply-form-${parentId}`);
                        if (replyForm) {
                            replyForm.classList.add('hidden');
                        }
                        
                        // Mostrar mensaje de éxito
                        showReplyFeedback(parentId, data.message, 'success');
                        
                        // Insertar la nueva respuesta
                        if (data.reply_html && repliesContainer) {
                            // Mostrar el contenedor de respuestas si estaba oculto
                            repliesContainer.classList.remove('hidden');
                            
                            // Crear elemento temporal para el HTML
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.reply_html;
                            const newReply = tempDiv.firstElementChild;
                            
                            // Agregar la nueva respuesta al final
                            repliesContainer.appendChild(newReply);
                            
                            // Actualizar contador de comentarios
                            if (commentsCount && data.comments_count) {
                                commentsCount.textContent = data.comments_count;
                            }
                        }
                        
                    } else {
                        throw new Error(data.message || 'Error al enviar la respuesta');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    showReplyFeedback(parentId, error.message || 'Ha ocurrido un error inesperado', 'error');
                } finally {
                    // Restaurar botón
                    submitButton.disabled = false;
                    submitButton.textContent = '{{ __("messages.answer") }}';
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        });
    });
</script>