@extends('layouts.app')

@section('content')

@php
    if (!function_exists('parseMentions')) {
        function parseMentions($text) {
            return preg_replace('/@([a-zA-Z0-9_]+)/', '<a href="/profile/$1" class="text-teal-400 hover:underline">@$1</a>', $text);
        }
    }
@endphp

<div class="max-w-3xl mx-auto space-y-8 bg-feedsbg min-h-screen py-2" id="feed-container">

    @foreach($posts as $post)
        <article class="post-item bg-feedsbg w-full overflow-visible flex flex-col text-white border-b border-grayComp" id="post-{{ $post->id }}">
            
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
            
            <div class="px-6 py-6 text-[19px]">
                <p>{!! nl2br(parseMentions(e($post->caption))) !!}</p>
            </div>
            
            @if($post->image)
            <div class="w-full bg-gray-100 flex justify-center">
                <img src="{{ Storage::url($post->image) }}" class="object-contain w-full h-auto max-h-[900px] block" loading="lazy">
            </div>
            @endif

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
                    {{-- ID DITAMBAHKAN UNTUK UPDATE COUNTER --}}
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
    @endforeach

</div>

<div id="loading" class="text-center py-4 hidden">
    <svg class="animate-spin h-8 w-8 text-teal-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div>
<div id="scroll-sentinel" class="h-10"></div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    // --- INFINITE SCROLL ---
    let nextPageUrl = "{{ $posts->nextPageUrl() }}";
    let isLoading = false;
    const sentinel = document.getElementById('scroll-sentinel');
    const loadingSpinner = document.getElementById('loading');
    const feedContainer = document.getElementById('feed-container');

    const observer = new IntersectionObserver(async (entries) => {
        if (entries[0].isIntersecting && nextPageUrl && !isLoading) {
            isLoading = true;
            loadingSpinner.classList.remove('hidden');
            try {
                const response = await fetch(nextPageUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const text = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                const newPosts = doc.querySelectorAll('.post-item');
                newPosts.forEach(post => { feedContainer.appendChild(post); });
                
                const urlObj = new URL(nextPageUrl);
                const currentPage = parseInt(urlObj.searchParams.get('page'));
                urlObj.searchParams.set('page', currentPage + 1);
                
                if (newPosts.length === 0) nextPageUrl = null;
                else nextPageUrl = urlObj.toString();
            } catch (error) { console.error('Error:', error); } 
            finally { isLoading = false; loadingSpinner.classList.add('hidden'); }
        }
    }, { rootMargin: '100px' });
    observer.observe(sentinel);

    // --- UI FUNCTIONS ---

    function toggleComments(postId) {
        document.getElementById(`comments-section-${postId}`).classList.toggle('hidden');
    }

    function toggleReplies(commentId, btnElement) {
        const container = document.getElementById(`replies-container-${commentId}`);
        if (container) {
            container.classList.toggle('hidden');
            if (btnElement) {
                const span = btnElement.querySelector('span');
                if (span) {
                    if (container.classList.contains('hidden')) {
                        span.innerText = span.innerText.replace('Hide', 'View');
                    } else {
                        span.innerText = span.innerText.replace('View', 'Hide');
                    }
                }
            }
        }
    }

    function replyToComment(rootId, commentId, username) {
        const formContainer = document.getElementById(`reply-form-container-${rootId}`);
        const inputBody = document.getElementById(`reply-input-${rootId}`);
        const inputParent = document.querySelector(`#reply-form-container-${rootId} input[name="parent_id"]`);

        if (!formContainer || !inputBody) { console.error("Root form not found"); return; }

        const isHidden = formContainer.classList.contains('hidden');
        const currentText = inputBody.value;
        const targetTag = `@${username} `;

        if (isHidden) {
            formContainer.classList.remove('hidden');
            inputBody.value = targetTag;
            if (inputParent) inputParent.value = commentId;
            inputBody.focus();
        } else {
            if (currentText.trim() === targetTag.trim()) {
                formContainer.classList.add('hidden');
            } else {
                inputBody.value = targetTag;
                if (inputParent) inputParent.value = commentId;
                inputBody.focus();
            }
        }
    }

    // --- AJAX ACTIONS ---

    // 1. SUBMIT COMMENT (NEW: UPDATE COUNTER & ORDER)
    async function submitCommentAjax(postId, rootId = null) {
        let inputBody, inputParent;
        
        if (rootId) {
            inputBody = document.getElementById(`reply-input-${rootId}`);
            inputParent = document.querySelector(`#reply-form-container-${rootId} input[name="parent_id"]`);
        } else {
            inputBody = document.querySelector(`#comments-section-${postId} input[name="body"]`);
        }

        if (!inputBody || !inputBody.value.trim()) { alert('Komentar kosong'); return; }

        const parentId = rootId && inputParent ? inputParent.value : null;
        let btn = inputBody.nextElementSibling;
        let originalText = btn.innerText;
        btn.innerText = '...'; btn.disabled = true;

        try {
            const res = await fetch(`/posts/${postId}/comments`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ body: inputBody.value.trim(), parent_id: parentId })
            });

            const data = await res.json();
            btn.innerText = originalText; btn.disabled = false;

            if (data.status === 'success') {
                inputBody.value = ''; 
                
                // UPDATE GLOBAL COMMENT COUNTER
                const postCounter = document.getElementById(`post-comment-count-${postId}`);
                if(postCounter) postCounter.innerText = parseInt(postCounter.innerText) + 1;

                if (rootId) {
                    // JIKA REPLY:
                    const replyContainer = document.getElementById(`replies-container-${rootId}`);
                    const btnContainer = document.getElementById(`reply-btn-container-${rootId}`);
                    const countText = document.getElementById(`reply-count-text-${rootId}`);

                    if(replyContainer) {
                        replyContainer.classList.remove('hidden');
                        // UPDATE: Pakai afterbegin agar reply baru user ada di paling atas
                        replyContainer.insertAdjacentHTML('afterbegin', data.html); 
                    }
                    
                    // Munculkan tombol View Replies jika sebelumnya hidden
                    if(btnContainer) btnContainer.classList.remove('hidden');
                    
                    // Update text "View X replies" (Manual Increment Regex)
                    if(countText) {
                        const currentCount = parseInt(countText.innerText.match(/\d+/)[0]);
                        countText.innerText = `Hide ${currentCount + 1} replies`; // Langsung set mode Hide karena otomatis kebuka
                    }

                    if(inputParent) inputParent.value = rootId;
                } else {
                    // JIKA KOMENTAR UTAMA:
                    const mainContainer = document.getElementById(`comments-container-${postId}`);
                    if(mainContainer) {
                        mainContainer.insertAdjacentHTML('afterbegin', data.html); // Paling atas
                        mainContainer.scrollTop = 0;
                    }
                }
            } else { alert('Gagal: ' + data.message); }
        } catch (error) { console.error(error); alert('Jaringan Error'); btn.innerText = originalText; btn.disabled = false; }
    }

    // 2. DELETE COMMENT (NEW: HIDE BUTTON IF 0 REPLIES)
    async function deleteComment(commentId) {
        if (!confirm('Hapus komentar?')) return;
        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            
            const data = await response.json();

            if (data.success) {
                const row = document.getElementById(`comment-row-${commentId}`);
                if (row) row.remove();

                // UPDATE GLOBAL COUNTER
                const postCounter = document.getElementById(`post-comment-count-${data.post_id}`);
                if(postCounter) postCounter.innerText = Math.max(0, parseInt(postCounter.innerText) - 1);

                // JIKA YANG DIHAPUS ADALAH REPLY
                if (data.parent_id) {
                    const btnContainer = document.getElementById(`reply-btn-container-${data.parent_id}`);
                    const countText = document.getElementById(`reply-count-text-${data.parent_id}`);
                    
                    if (data.remaining_replies <= 0) {
                        // Sembunyikan tombol jika reply habis
                        if(btnContainer) btnContainer.classList.add('hidden');
                    } else {
                        // Update angka jika masih ada sisa
                        if(countText) {
                            // Cek apakah sedang View atau Hide
                            const mode = countText.innerText.includes('Hide') ? 'Hide' : 'View';
                            countText.innerText = `${mode} ${data.remaining_replies} replies`;
                        }
                    }
                }
            }
        } catch (error) { console.error(error); }
    }

    // ... Toggle Like/Upvote code remains same ...
    async function toggleLike(postId) {
        const btn = document.getElementById(`like-btn-${postId}`);
        const countSpan = document.getElementById(`like-count-${postId}`);
        const icon = btn.querySelector('svg');
        try {
            const response = await fetch(`/posts/${postId}/like`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            countSpan.innerText = data.count;
            if (data.status === 'liked') {
                btn.classList.add('text-pink-600'); btn.classList.remove('hover:text-pink-600'); icon.classList.add('fill-current');
            } else {
                btn.classList.remove('text-pink-600'); btn.classList.add('hover:text-pink-600'); icon.classList.remove('fill-current');
            }
        } catch (error) { console.error(error); }
    }

    async function toggleCommentLike(commentId) {
        const btn = document.getElementById(`comment-like-btn-${commentId}`);
        const countSpan = document.getElementById(`comment-like-count-${commentId}`);
        const icon = btn.querySelector('svg');
        try {
            const response = await fetch(`/comments/${commentId}/like`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            countSpan.innerText = data.count;
            if (data.count > 0) countSpan.classList.remove('hidden'); else countSpan.classList.add('hidden');
            if (data.status === 'liked') {
                btn.classList.add('text-pink-600'); btn.classList.remove('text-gray-500'); icon.classList.add('fill-current');
            } else {
                btn.classList.remove('text-pink-600'); btn.classList.add('text-gray-500'); icon.classList.remove('fill-current');
            }
        } catch (error) { console.error(error); }
    }

    async function toggleUpvote(postId) {
        const btn = document.getElementById(`upvote-btn-${postId}`);
        const countSpan = document.getElementById(`upvote-count-${postId}`);
        const icon = btn.querySelector('svg');
        try {
            const response = await fetch(`/posts/${postId}/upvote`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            countSpan.innerText = data.count;
            if (data.status === 'upvoted') {
                btn.classList.add('text-green-500'); btn.classList.remove('hover:text-green-500'); icon.classList.add('fill-current');
            } else {
                btn.classList.remove('text-green-500'); btn.classList.add('hover:text-green-500'); icon.classList.remove('fill-current');
            }
        } catch (error) { console.error(error); }
    }
</script>
@endsection