<div class="max-w-3xl mx-auto space-y-8 bg-feedsbg min-h-screen py-2" id="feed-container">

        <article class="post-item bg-feedsbg w-full flex flex-col text-white border-b border-grayComp relative transition-all duration-200" id="post-{{ $post->id }}">

            <header class="flex items-center justify-between px-6 pt-4 relative ">
                <a href="{{ route('profile.show', $post->user->id) }}" class="flex items-center space-x-4 group">
                    <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center font-semibold overflow-hidden border border-transparent group-hover:border-teal-500 transition">
                        @if($post->user->avatar)
                            <img src="{{ Storage::url($post->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white">{{ strtoupper(substr($post->user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="font-semibold group-hover:text-teal-400 transition">{{ $post->user->name }}</div>
                        <div class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </a>
                @if(auth()->id() === $post->user->id)
                    <div class="relative">
                        <button onclick="togglePostMenu(event, {{ $post->id }})" 
                                class="text-gray-400 hover:text-white p-2 rounded-full hover:bg-gray-800 transition focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                        <div id="post-menu-{{ $post->id }}" class="hidden absolute right-0 mt-2 w-32 bg-grayComp border border-grayShadow rounded-lg shadow-lg z-1 overflow-hidden">
                            <button onclick="deletePost({{ $post->id }})" class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-600 hover:text-white flex items-center gap-2 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                @endif
            </header>
            <div class="px-6 py-6 text-[19px]">
                <p>{!! nl2br($post->formatted_caption) !!}</p>
            </div>
            
            @if($post->image)
            <div class="w-full bg-darkest flex justify-center">
                <img src="{{ Storage::url($post->image) }}" class="object-contain w-full h-auto max-h-[900px] block" loading="lazy">
            </div>
            @endif

            <div class="px-10 py-5 flex flex-row gap-60 text-gray-400 select-none border-t border-grayComp/20">
                <button onclick="toggleLike({{ $post->id }})" id="like-btn-{{ $post->id }}" class="flex items-center gap-2 transition group {{ $post->isLikedByAuthUser() ? 'text-pink-600' : 'hover:text-pink-600' }}">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.like class="w-6 h-6 {{ $post->isLikedByAuthUser() ? 'fill-current' : '' }}" />
                    </div>
                    <span id="like-count-{{ $post->id }}">{{ $post->likes_count }}</span>
                </button>

                <button onclick="toggleComments({{ $post->id }})" class="flex items-center gap-2 hover:text-blue-500 transition group">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.comment class="w-6 h-6" />
                    </div>
                    <span id="post-comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                </button>

                <button onclick="toggleUpvote({{ $post->id }})" id="upvote-btn-{{ $post->id }}" class="flex items-center gap-2 transition group {{ $post->isUpvotedByAuthUser() ? 'text-green-500' : 'hover:text-green-500' }}">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.upvote class="w-6 h-6 {{ $post->isUpvotedByAuthUser() ? 'fill-current' : '' }}" />
                    </div>
                    <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes_count }}</span>
                </button>
            </div>

            <div id="comments-section-{{ $post->id }}" class="hidden px-6 pb-6 bg-[#151515] border-t border-grayComp">
                <form onsubmit="event.preventDefault(); submitCommentAjax({{ $post->id }});" class="flex gap-3 mb-4 pt-4">
                    @csrf
                    <div class="w-8 h-8 rounded-full bg-gray-600 flex-shrink-0 overflow-hidden">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-xs">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <input type="text" name="body" placeholder="Write a comment..." class="flex-1 bg-transparent border-b border-gray-700 text-white px-2 py-1 text-sm focus:outline-none focus:border-teal-500 transition" autocomplete="off">
                    <button type="submit" class="text-teal-500 font-bold text-sm disabled:opacity-50">Post</button>
                </form>

                <div id="comments-container-{{ $post->id }}" class="space-y-6 max-h-[500px] overflow-y-auto custom-scrollbar pr-2">
                    @foreach($post->comments->whereNull('parent_id') as $comment)
                        @include('partials.comment-item', ['comment' => $comment, 'root_id' => $comment->id])
                    @endforeach
                </div>
            </div>

        </article>
    

</div>

<script>
    function togglePostMenu(event, postId) {
        event.stopPropagation(); 

        const menu = document.getElementById(`post-menu-${postId}`);
        const postCard = document.getElementById(`post-${postId}`);
        document.querySelectorAll('[id^="post-menu-"]').forEach(el => {
            if (el.id !== `post-menu-${postId}`) el.classList.add('hidden');
        });

        document.querySelectorAll('.post-item').forEach(el => el.classList.remove('z-1', 'relative'));
        document.querySelectorAll('.post-item').forEach(el => el.classList.add('relative')); // Kembalikan relative default

        menu.classList.toggle('hidden');

        if (!menu.classList.contains('hidden')) {
            postCard.classList.add('z-1');
        } else {
            postCard.classList.remove('z-1');
        }
    }

    document.addEventListener('click', function(event) {
        const isButton = event.target.closest('button[onclick^="togglePostMenu"]');
        const isMenu = event.target.closest('[id^="post-menu-"]');
        
        if (!isButton && !isMenu) {
            document.querySelectorAll('[id^="post-menu-"]').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.post-item').forEach(el => el.classList.remove('z-1'));
        }
    });

    async function deletePost(postId) {
        if (!confirm('Are you sure you want to delete this post?')) return;

        try {
            const response = await fetch(`/posts/${postId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (data.success) {
                const postElement = document.getElementById(`post-${postId}`);
                postElement.style.transition = "all 0.3s ease";
                postElement.style.opacity = "0";
                postElement.style.transform = "scale(0.95)";
                
                setTimeout(() => {
                    postElement.remove();
                }, 300);
            } else {
                alert(data.message || 'Gagal menghapus postingan.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem.');
        }
    }
</script>