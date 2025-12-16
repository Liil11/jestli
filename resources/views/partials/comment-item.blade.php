

@php
use App\Models\User;

if (!function_exists('parseMentionsPartial')) {
    function parseMentionsPartial($text) {
        return preg_replace_callback('/@([a-zA-Z0-9_]+)/', function ($matches) {
            $username = $matches[1];

            if (!User::where('name', $username)->exists()) {
                return '@' . $username;
            }

            return '<a href="' 
                . route('profile.username', $username) 
                . '" class="text-teal-400 hover:underline">@'
                . $username . '</a>';
        }, $text);
    }
}

$rootId = $root_id ?? $comment->id;
$replyCount = $comment->replies->count();
@endphp

<div id="comment-row-{{ $comment->id }}" class="group/comment fade-in mb-4">
    
    <div class="flex gap-3 items-start">
        <a href="{{ route('profile.show', $comment->user->id) }}" class="w-8 h-8 rounded-full bg-gray-700 flex-shrink-0 overflow-hidden mt-1">
            @if($comment->user->avatar)
                <img src="{{ Storage::url($comment->user->avatar) }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center font-bold text-xs text-white">
                    {{ substr($comment->user->name, 0, 1) }}
                </div>
            @endif
        </a>

        <div class="flex-1">
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.show', $comment->user->id) }}" class="font-bold text-sm hover:underline text-white">
                    {{ $comment->user->name }}
                </a>
                <span class="text-gray-500 text-xs">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>
            
            <p class="text-gray-300 text-sm mt-0.5">
                {!! nl2br(parseMentionsPartial(e($comment->body))) !!}
            </p>

            <div class="flex items-center gap-4 mt-2 text-xs">
                <button onclick="replyToComment({{ $rootId }}, {{ $comment->id }}, '{{ $comment->user->name }}')"
                        class="text-gray-500 hover:text-white font-semibold">
                    Reply
                </button>
                
                <button onclick="toggleCommentLike({{ $comment->id }})"
                        id="comment-like-btn-{{ $comment->id }}"
                        class="flex items-center gap-1 transition {{ $comment->isLikedByAuthUser() ? 'text-pink-600' : 'hover:text-pink-600 text-gray-500' }}">
                    <div class="w-4 h-4 flex items-center justify-center">
                        <x-icons.like class="w-3 h-3 {{ $comment->isLikedByAuthUser() ? 'fill-current' : '' }}" />
                    </div>
                    <span id="comment-like-count-{{ $comment->id }}" class="{{ $comment->likes_count > 0 ? '' : 'hidden' }}">
                        {{ $comment->likes_count }}
                    </span>
                </button>

                @if(auth()->id() === $comment->user_id)
                    <button onclick="deleteComment({{ $comment->id }})"
                            class="text-gray-500 hover:text-red-500"
                            title="Delete">
                        🗑
                    </button>
                @endif
            </div>

            @if(!$comment->parent_id)
                <div id="reply-form-container-{{ $comment->id }}" class="hidden mt-3 fade-in">
                    <form onsubmit="event.preventDefault(); submitCommentAjax({{ $comment->post_id }}, {{ $comment->id }});"
                          class="flex gap-3 mb-4 pt-4">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <div class="w-6 border-t border-gray-700"></div>
                        <input type="text"
                               id="reply-input-{{ $comment->id }}"
                               name="body"
                               class="w-full bg-transparent border-b border-gray-700 text-sm text-gray-300 focus:outline-none focus:border-teal-500 py-1"
                               placeholder="Reply..."
                               autocomplete="off">
                        <button type="submit" class="text-xs font-bold text-teal-500">Send</button>
                    </form>
                </div>

                <div id="reply-btn-container-{{ $comment->id }}" class="mt-2 {{ $replyCount > 0 ? '' : 'hidden' }}">
                    <button onclick="toggleReplies('{{ $comment->id }}', this)"
                            class="flex items-center gap-2 text-xs text-gray-500 hover:text-white font-medium">
                        <div class="w-6 border-t border-gray-700"></div>
                        <span id="reply-count-text-{{ $comment->id }}">
                            View {{ $replyCount }} replies
                        </span>
                    </button>
                </div>

                <div id="replies-container-{{ $comment->id }}"
                     class="hidden mt-2 ml-4 pl-4 border-l border-gray-800 space-y-3">
                    @foreach($comment->replies as $reply)
                        @include('partials.comment-item', [
                            'comment' => $reply,
                            'root_id' => $comment->id
                        ])
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
