{{-- File: resources/views/partials/post-item.blade.php --}}
<article class="post-item bg-feedsbg w-full overflow-visible flex flex-col text-white border-b border-grayComp" id="post-{{ $post->id }}">
    
    {{-- Header Post --}}
    <header class="flex items-center justify-between px-6 pt-4 relative">
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
    </header>
    
    {{-- Caption --}}
    <div class="px-6 py-6 text-[19px]">
        {{-- Pastikan fungsi parseMentions ada di Helper atau dipanggil di parent --}}
        <p>{!! nl2br(preg_replace('/@([a-zA-Z0-9_]+)/', '<a href="/profile/$1" class="text-teal-400 hover:underline z-10 relative" onclick="event.stopPropagation()">@$1</a>', e($post->caption))) !!}</p>
    </div>
    
    {{-- Image --}}
    @if($post->image)
    <div class="w-full bg-gray-100 flex justify-center">
        <img src="{{ Storage::url($post->image) }}" class="object-contain w-full h-auto max-h-[900px] block" loading="lazy">
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="px-10 py-5 flex flex-row gap-60 text-gray-400 select-none">
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

    {{-- Comments Section --}}
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