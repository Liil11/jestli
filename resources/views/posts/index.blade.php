@extends('layouts.app')

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
            
            <!-- Image (no fixed parent height, image decides height) -->
            <div class="w-full bg-gray-100 flex justify-center">
                <img
                  src="{{ Storage::url($post->image) }}"
                  alt="Post image"
                  class="object-contain w-full h-auto max-h-[900px] block"
                  loading="lazy"
                >
            </div>

            <!-- Caption -->
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

                 <!-- Comments section -->
    <div class="comments-section">
        @foreach($post->comments as $comment)
            <div class="comment" data-id="{{ $comment->id }}">
                <strong>{{ $comment->user->name }}</strong>
                <p>{{ $comment->content }}</p>
                @if(auth()->id() === $comment->user_id)
                    <button class="delete-comment" data-id="{{ $comment->id }}">Delete</button>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Comment form -->
    <form class="comment-form" data-post-id="{{ $post->id }}">
        <input type="text" name="content" placeholder="Add a comment..." required>
        <button type="submit">Post</button>
    </form>
</div>

<script>
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const postId = e.target.dataset.postId;

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
                    content: formData.get('content')
                })
            });

            const data = await response.json();
            if(response.ok) {
                addCommentToDOM(data.comment, data.user);
                e.target.reset();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});

function addCommentToDOM(comment, user) {
    const commentsSection = document.querySelector('.comments-section');
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment';
    commentDiv.dataset.id = comment.id;
    commentDiv.innerHTML = `
        <strong>${user.name}</strong>
        <p>${comment.content}</p>
        <button class="delete-comment" data-id="${comment.id}">Delete</button>
    `;
    commentsSection.appendChild(commentDiv);
}

document.querySelectorAll('.delete-comment').forEach(button => {
    button.addEventListener('click', async (e) => {
        const commentId = e.target.dataset.id;
        if (!confirm('Delete this comment?')) return;

        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if(response.ok) {
                document.querySelector(`.comment[data-id="${commentId}"]`).remove();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
                <button class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.upvote class="w-full h-full" />
                    </div>
                    <span>80</span>
                </button>
            </div>



        </article>
    @endforeach

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection
