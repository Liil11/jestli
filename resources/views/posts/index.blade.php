@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="max-w-3xl mx-auto space-y-8 bg-feedsbg min-h-screen py-2">

    @foreach($posts as $post)
        <article class="bg-feedsbg w-full overflow-visible flex flex-col text-white">
            <!-- Header -->
            <header class="flex items-center justify-between px-6 pt-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold">{{ $post->user->name }}</div>
                        <div class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </header>
            
            <!-- Caption -->
            <div class="px-6 py-6 text-[19px]">
                <p class="">
                    {{ $post->caption }}
                </p>
            </div>
            
            <!-- Image -->
            <div class="w-full bg-gray-100 flex justify-center">
                <img
                  src="{{ Storage::url($post->image) }}"
                  alt="Post image"
                  class="object-contain w-full h-auto max-h-[900px] block"
                  loading="lazy"
                >
            </div>

            <!-- Actions -->
            <div class="px-10 py-5 flex flex-row gap-60">
                <button
                    class="like-btn flex items-center gap-1 text-xl"
                    data-post="{{ $post->id }}"
                >
                    <span class="icon select-none">
                        {{ $post->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}
                    </span>
                    <span class="count text-sm text-gray-600">
                        {{ $post->likes->count() }}
                    </span>
                </button>

                <!-- Comments Toggle Button -->
                <button 
                    class="toggle-comments flex items-center gap-2"
                    data-post-id="{{ $post->id }}"
                >
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.comment class="w-full h-full" />
                    </div>
                    <span>{{ $post->comments->count() }}</span>
                </button>
                
                <button class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.upvote class="w-full h-full" />
                    </div>
                    <span>80</span>
                </button>
            </div>

            <!-- Comments Section -->
            <div 
                id="comments-{{ $post->id }}" 
                class="hidden px-10 pb-4 space-y-3 border-t border-gray-700"
            >
                <!-- Existing Comments -->
                <div class="space-y-3 max-h-60 overflow-y-auto">
                    @foreach($post->comments as $comment)
                        <div class="flex items-start gap-3 py-2" data-comment-id="{{ $comment->id }}">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-black text-sm font-semibold">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-800 rounded-2xl px-4 py-2">
                                    <div class="font-semibold text-sm">{{ $comment->user->name }}</div>
                                    <p class="text-sm">{{ $comment->content }}</p>
                                </div>
                                @if(auth()->id() === $comment->user_id)
                                    <button 
                                        class="delete-comment text-xs text-red-500 mt-1"
                                        data-comment-id="{{ $comment->id }}"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Comment Form -->
                <form 
                    class="comment-form flex items-center gap-3 pt-3" 
                    data-post-id="{{ $post->id }}"
                >
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-black font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 flex">
                        <input
                            type="text"
                            name="content"
                            placeholder="Add a comment..."
                            class="flex-1 bg-transparent border-b border-gray-600 py-2 px-2 text-sm focus:outline-none focus:border-white"
                            required
                        >
                        <button 
                            type="submit"
                            class="ml-2 text-blue-500 text-sm font-semibold"
                        >
                            Post
                        </button>
                    </div>
                </form>
            </div>
        </article>
    @endforeach

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>

<script>
// Toggle comments visibility
document.querySelectorAll('.toggle-comments').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const postId = button.dataset.postId;
        const commentsSection = document.getElementById(`comments-${postId}`);
        commentsSection.classList.toggle('hidden');
    });
});

// Submit new comment
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const postId = e.target.dataset.postId;
        const content = formData.get('content').trim();

        if (!content) {
            alert('Comment cannot be empty');
            return;
        }

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Posting...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('/comments', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    post_id: postId,
                    content: content
                })
            });

            const data = await response.json();
            
            if(response.ok) {
                addCommentToDOM(data.comment, data.user, postId);
                e.target.reset();
                // Update comment count
                updateCommentCount(postId, 1);
            } else {
                console.error('Error:', data);
                alert('Failed to post comment: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Network Error:', error);
            alert('Network error. Please try again.');
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
});

// Delete comment
document.querySelectorAll('.delete-comment').forEach(button => {
    button.addEventListener('click', async (e) => {
        const commentId = e.target.dataset.commentId;
        if (!confirm('Delete this comment?')) return;

        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        const postId = commentElement.closest('[id^="comments-"]').id.replace('comments-', '');

        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if(response.ok) {
                commentElement.remove();
                // Update comment count
                updateCommentCount(postId, -1);
            } else {
                const data = await response.json();
                alert('Failed to delete comment: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        }
    });
});

function addCommentToDOM(comment, user, postId) {
    const commentsContainer = document.querySelector(`#comments-${postId} .space-y-3`);
    const commentElement = document.createElement('div');
    commentElement.className = 'flex items-start gap-3 py-2';
    commentElement.dataset.commentId = comment.id;
    commentElement.innerHTML = `
        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-black text-sm font-semibold">
            ${user.name.charAt(0).toUpperCase()}
        </div>
        <div class="flex-1">
            <div class="bg-gray-800 rounded-2xl px-4 py-2">
                <div class="font-semibold text-sm">${user.name}</div>
                <p class="text-sm">${comment.content}</p>
            </div>
            ${comment.user_id === {{ auth()->id() ?? 0 }} ? 
                `<button 
                    class="delete-comment text-xs text-red-500 mt-1" 
                    data-comment-id="${comment.id}"
                >
                    Delete
                </button>` : ''}
        </div>
    `;
    commentsContainer.appendChild(commentElement);
    
    // Add event listener to the new delete button
    commentElement.querySelector('.delete-comment')?.addEventListener('click', handleDeleteClick);
}

function updateCommentCount(postId, change) {
    const commentButton = document.querySelector(`[data-post-id="${postId}"]`);
    if (commentButton) {
        const span = commentButton.querySelector('span');
        let count = parseInt(span.textContent) || 0;
        count += change;
        span.textContent = count;
    }
}

function handleDeleteClick(e) {
    const commentId = e.target.dataset.commentId;
    if (!confirm('Delete this comment?')) return;

    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
    const postId = commentElement.closest('[id^="comments-"]').id.replace('comments-', '');

    fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if(response.ok) {
            commentElement.remove();
            updateCommentCount(postId, -1);
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Unknown error');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting comment: ' + error.message);
    });
}

// Attach delete listeners to existing buttons
document.querySelectorAll('.delete-comment').forEach(button => {
    button.addEventListener('click', handleDeleteClick);
});
</script>
@endsection