@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#121212] text-white pb-20">

    {{-- HEADER PROFILE (Banner & Avatar) --}}
    <div class="relative w-full h-64 bg-gray-800">
        @if($user->banner)
            <img src="{{ Storage::url($user->banner) }}" class="w-full h-full object-cover" alt="Banner">
        @else
            <div class="w-full h-full bg-gradient-to-r from-gray-800 to-gray-900 flex items-center justify-center text-gray-700">
                <span class="text-4xl opacity-20">No Banner</span>
            </div>
        @endif

        {{-- 
            FIX FINAL HITBOX: 
            Menggunakan 'left-1/2 -translate-x-1/2' agar container HANYA seukuran avatar.
            Tidak ada lagi layer transparan yang menutupi tombol di kiri-kanan.
        --}}
        <div class="absolute -bottom-16 left-1/2 transform -translate-x-1/2 z-2">
            <div class="w-32 h-32 rounded-full border-4 border-[#121212] overflow-hidden bg-gray-700">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover" alt="Avatar">
                @else
                    <div class="w-full h-full flex items-center justify-center text-4xl font-bold bg-teal-600">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- INFO USER --}}
    <div class="mt-20 text-center px-4 relative z-2">
        <div class="flex items-center justify-center gap-3 mb-2">
            <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
            
            @auth
                @if(auth()->id() === $user->id)
                    <button id="openEditProfileBtn" class="bg-teal-600/20 text-teal-400 text-[11px] font-bold px-3 py-0.5 rounded-full border border-teal-600/50 hover:bg-teal-600 hover:text-white transition tracking-wider relative z-2">
                        Edit
                    </button>
                @else
                    {{-- 
                        FIX FINAL TOMBOL:
                        Tambahkan 'relative z-50 cursor-pointer'.
                        Ini memaksa tombol bisa diklik meskipun ada elemen lain di sekitarnya.
                    --}}
                    @php
                        $isFollowing = auth()->user()->isFollowing($user);
                    @endphp
                    <button 
                        onclick="toggleFollow(this, {{ $user->id }})"
                        data-following="{{ $isFollowing ? 'true' : 'false' }}"
                        class="relative z-2 cursor-pointer px-4 py-1 rounded-full text-xs font-bold transition-all duration-200 border
                        {{ $isFollowing 
                            ? 'bg-transparent text-gray-300 border-gray-600 hover:border-red-500 hover:text-red-500' 
                            : 'bg-white text-black border-white hover:bg-gray-200' 
                        }}">
                        <span class="btn-text pointer-events-none">{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                    </button>
                @endif
            @endauth
        </div>

        <p class="text-gray-400 text-sm max-w-md mx-auto mt-2 leading-relaxed">
            {{ $user->description ?? 'No description yet.' }}
        </p>

        {{-- STATS COUNTER --}}
        <div class="flex justify-center gap-6 mt-5 text-xs font-medium text-gray-400 relative z-2">
            {{-- Following --}}
            <div onclick="openUserModal('following')" class="cursor-pointer hover:text-white transition group">
                <span class="text-white font-bold text-sm group-hover:underline decoration-teal-500 underline-offset-4" id="following-count">
                    {{ $user->followings_count }}
                </span> 
                Following
            </div>

            {{-- Followers --}}
            <div onclick="openUserModal('followers')" class="cursor-pointer hover:text-white transition group">
                <span class="text-white font-bold text-sm group-hover:underline decoration-teal-500 underline-offset-4" id="followers-count">
                    {{ $user->followers_count }}
                </span> 
                Followers
            </div>

            {{-- Post Count --}}
            <div>
                <span class="text-white font-bold text-sm">{{ $user->posts_count }}</span> Post
            </div>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="mt-8 pt-2 border-b border-grayShadow sticky top-[72px] bg-[#121212] z-10">
        <div class="flex justify-center gap-12 text-sm font-medium">
            <button onclick="switchTab('posts')" id="tab-btn-posts" class="tab-btn pb-3 border-b-2 border-teal-500 text-white px-2 transition-colors">
                Post
            </button>
            <button onclick="switchTab('replies')" id="tab-btn-replies" class="tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-300 px-2 transition-colors">
                Replies
            </button>
            <button onclick="switchTab('media')" id="tab-btn-media" class="tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-300 px-2 transition-colors">
                Media
            </button>
        </div>
    </div>

    <div class="max-w-3xl mx-auto min-h-[300px]">
        
        {{-- TAB 1: POSTS --}}
        <div id="content-posts" class="tab-content px-4 md:px-0">
            @forelse($posts as $post)
                @include('partials.post-item', ['post' => $post])
            @empty
                <div class="text-center text-gray-500 py-10">No posts available.</div>
            @endforelse
        </div>

        {{-- TAB 2: REPLIES --}}
        <div id="content-replies" class="tab-content hidden py-0">
            @forelse($replies as $reply)
                <article onclick="window.location='{{ route('posts.show', $reply->post_id) }}'" 
                         class="bg-[#121212] border-b border-gray-800 p-4 hover:bg-white/5 transition cursor-pointer group">
                    <div class="mb-3 ml-12">
                        <div class="text-gray-500 text-xs flex items-center gap-1 mb-1">
                            <span class="text-gray-400">Replying to</span>
                            @if($reply->parent && $reply->parent->user)
                                <span class="text-teal-500 font-bold">{{ '@'.$reply->parent->user->name }}</span>
                            @else
                                <span class="text-teal-500 font-bold">{{ '@'.$reply->post->user->name }}</span>
                                <span class="text-gray-600 text-[10px] px-1 border border-gray-700 rounded">Post</span>
                            @endif
                        </div>
                        <div class="border-l-2 border-gray-700 pl-3 py-1 text-gray-400 text-sm italic line-clamp-1 opacity-70">
                            @if($reply->parent)
                                "{{ Str::limit($reply->parent->body, 80) }}"
                            @else
                                "{{ Str::limit($reply->post->caption, 80) }}"
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-600 flex-shrink-0 overflow-hidden border border-gray-700">
                             @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                             @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-white bg-teal-600">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                             @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white text-sm group-hover:underline decoration-teal-500 underline-offset-4">
                                    {{ $user->name }}
                                </span>
                                <span class="text-gray-500 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-200 text-base mt-1 leading-relaxed">
                                {{ $reply->body }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-gray-500">
                    <p>No replies yet.</p>
                </div>
            @endforelse
        </div>

        {{-- TAB 3: MEDIA --}}
        <div id="content-media" class="tab-content hidden py-1">
            <div class="grid grid-cols-3 gap-1">
                @forelse($media as $item)
                    <a href="{{ route('posts.show', $item->id) }}" class="relative group aspect-square bg-gray-800 overflow-hidden cursor-pointer block">
                        <img src="{{ Storage::url($item->image) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110 group-hover:opacity-60" alt="Media Content">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-6 text-white font-bold backdrop-blur-[2px]">
                            <div class="w-10 h-6 flex items-center justify-center">
                                <x-icons.like class="w-6 h-6 fill-white drop-shadow-md"/> 
                                <span class="pl-2 text-sm drop-shadow-md">{{ $item->likes_count }}</span>
                            </div>
                            <div class="w-10 h-6 flex items-center justify-center">
                                <x-icons.comment class="w-6 h-6 fill-white drop-shadow-md"/> 
                                <span class="pl-2 text-sm drop-shadow-md">{{ $item->comments_count }}</span>
                            </div>
                             <div class="w-10 h-6 flex items-center justify-center">
                                <x-icons.upvote class="w-6 h-6 drop-shadow-md"/> 
                                <span class="pl-2 text-sm drop-shadow-md">{{ $item->upvotes_count }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 flex flex-col items-center justify-center py-20 text-gray-500">
                        <p>No media posts found.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- MODAL EDIT PROFILE --}}
@if(auth()->id() === $user->id)
    <div id="editProfileModal" class="fixed inset-0 z-[60] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#121212] w-full max-w-2xl rounded-lg shadow-2xl border border-gray-800 transform scale-95 transition-transform duration-300" id="editModalContent">
            <div class="p-6 pb-2">
                <h2 class="text-white text-2xl font-bold">Edit Profile</h2>
            </div>
            <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-6">
                        <div class="grid grid-cols-3 items-center gap-4">
                            <label class="text-gray-300 text-base">Name</label>
                            <div class="col-span-2 relative">
                                <input type="text" name="name" value="{{ $user->name }}" class="w-full bg-black border border-gray-700 rounded p-2 text-white focus:border-teal-500 outline-none pr-8">
                                <span class="absolute right-3 top-2.5 text-gray-500">✎</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 items-start gap-4">
                            <label class="text-gray-300 text-base mt-2">Description</label>
                            <div class="col-span-2 relative">
                                <textarea name="description" rows="4" class="w-full bg-black border border-gray-700 rounded p-2 text-white focus:border-teal-500 outline-none resize-none pr-8">{{ $user->description }}</textarea>
                                <span class="absolute right-3 bottom-3 text-gray-500">✎</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 items-center gap-4">
                            <label class="text-gray-300 text-base">Banner</label>
                            <div class="col-span-2">
                                <label class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded text-sm cursor-pointer transition">
                                    Change Banner
                                    <input type="file" name="banner" class="hidden" onchange="previewFile(this, 'bannerPreview')">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-700 border border-gray-600">
                            @if($user->avatar) 
                                <img src="{{ Storage::url($user->avatar) }}" id="avatarPreview" class="w-full h-full object-cover"> 
                            @else 
                                <img id="avatarPreview" class="w-full h-full object-cover hidden">
                                <div class="w-full h-full flex items-center justify-center font-bold text-2xl text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div> 
                            @endif
                        </div>
                        <label class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded text-sm cursor-pointer transition">
                            Change Photo
                            <input type="file" name="avatar" class="hidden" onchange="previewFile(this, 'avatarPreview')">
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-6 mt-2">
                    <button type="button" id="cancelEditBtn" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded font-medium transition">Cancel</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded font-medium transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- MODAL LIST USER (FOLLOWERS / FOLLOWING) --}}
<div id="userListModal" class="fixed inset-0 z-[70] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#121212] w-full max-w-sm rounded-xl shadow-2xl border border-gray-800 flex flex-col max-h-[60vh]">
        
        {{-- Header Modal --}}
        <div class="p-4 border-b border-gray-800 flex justify-between items-center bg-[#121212] rounded-t-xl sticky top-0 z-10">
            <h3 id="modalTitle" class="text-white font-bold text-lg capitalize tracking-wide">Followers</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Content List (Scrollable) --}}
        <div class="overflow-y-auto p-2 flex-1 custom-scrollbar">
            
            {{-- LIST FOLLOWERS --}}
            <div id="list-followers" class="user-list-content hidden space-y-1">
                @forelse($followers as $f)
                    <a href="{{ route('profile.show', $f->id) }}" class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-lg transition group">
                        
                        {{-- Avatar Logic (Database / Initials) --}}
                        <div class="w-10 h-10 rounded-full bg-gray-700 overflow-hidden flex-shrink-0 border border-transparent group-hover:border-gray-600">
                            @if($f->avatar)
                                <img src="{{ Storage::url($f->avatar) }}" class="w-full h-full object-cover" alt="{{ $f->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-white bg-teal-600 text-sm">
                                    {{ strtoupper(substr($f->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Nama User --}}
                        <span class="text-gray-200 font-semibold text-sm group-hover:text-white truncate">
                            {{ $f->name }}
                        </span>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                        <p class="text-sm">No followers yet.</p>
                    </div>
                @endforelse
            </div>

            {{-- LIST FOLLOWING --}}
            <div id="list-following" class="user-list-content hidden space-y-1">
                @forelse($followings as $f)
                    <a href="{{ route('profile.show', $f->id) }}" class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-lg transition group">
                        
                        {{-- Avatar Logic --}}
                        <div class="w-10 h-10 rounded-full bg-gray-700 overflow-hidden flex-shrink-0 border border-transparent group-hover:border-gray-600">
                            @if($f->avatar)
                                <img src="{{ Storage::url($f->avatar) }}" class="w-full h-full object-cover" alt="{{ $f->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-white bg-teal-600 text-sm">
                                    {{ strtoupper(substr($f->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Nama User --}}
                        <span class="text-gray-200 font-semibold text-sm group-hover:text-white truncate">
                            {{ $f->name }}
                        </span>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                        <p class="text-sm">Not following anyone yet.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    // --- TAB SWITCHING ---
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-teal-500', 'text-white');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById('content-' + tabName).classList.remove('hidden');
        const activeBtn = document.getElementById('tab-btn-' + tabName);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-teal-500', 'text-white');
    }

    // --- FOLLOW SYSTEM LOGIC (ADDED) ---
    async function toggleFollow(button, userId) {
        let isFollowing = button.getAttribute('data-following') === 'true';
        let btnText = button.querySelector('.btn-text');
        
        button.disabled = true;
        button.style.opacity = '0.7';

        try {
            const response = await fetch(`/user/${userId}/follow`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();

            if (data.status === 'followed') {
                button.setAttribute('data-following', 'true');
                btnText.innerText = 'Following';
                button.className = "relative z-50 cursor-pointer px-4 py-1 rounded-full text-xs font-bold transition-all duration-200 border bg-transparent text-gray-300 border-gray-600 hover:border-red-500 hover:text-red-500";
                updateFollowerCount(1);
            } else {
                button.setAttribute('data-following', 'false');
                btnText.innerText = 'Follow';
                button.className = "relative z-50 cursor-pointer px-4 py-1 rounded-full text-xs font-bold transition-all duration-200 border bg-white text-black border-white hover:bg-gray-200";
                updateFollowerCount(-1);
            }
        } catch (error) { console.error('Error:', error); alert('Gagal memproses follow.'); } 
        finally { button.disabled = false; button.style.opacity = '1'; }
    }

    function updateFollowerCount(change) {
        const counter = document.getElementById('followers-count');
        if(counter) {
            let current = parseInt(counter.innerText);
            counter.innerText = Math.max(0, current + change);
        }
    }

    // --- MODAL USER LIST (Followers/Following) ---
    function openUserModal(type) {
        const modal = document.getElementById('userListModal');
        const title = document.getElementById('modalTitle');
        document.querySelectorAll('.user-list-content').forEach(el => el.classList.add('hidden'));
        if (type === 'followers') {
            title.innerText = 'Followers';
            document.getElementById('list-followers').classList.remove('hidden');
        } else {
            title.innerText = 'Following';
            document.getElementById('list-following').classList.remove('hidden');
        }
        modal.classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userListModal').classList.add('hidden');
    }
    
    // Close modal on click outside
    const userModal = document.getElementById('userListModal');
    if(userModal){
        userModal.addEventListener('click', function(e) {
            if(e.target === this) closeUserModal();
        });
    }

    // --- MODAL EDIT PROFILE ---
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('editProfileModal');
        if(modal) {
            const openBtn = document.getElementById('openEditProfileBtn');
            const cancelBtn = document.getElementById('cancelEditBtn');
            const modalContent = document.getElementById('editModalContent');

            function openModal() {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }, 10);
            }
            function closeModal() {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
                setTimeout(() => { modal.classList.add('hidden'); }, 200);
            }
            if(openBtn) openBtn.addEventListener('click', openModal);
            if(cancelBtn) cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        }
    });

    function previewFile(input, imgId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(imgId);
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- POST INTERACTION LOGIC ---
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
                    span.innerText = span.innerText.replace(container.classList.contains('hidden') ? 'Hide' : 'View', container.classList.contains('hidden') ? 'View' : 'Hide');
                }
            }
        }
    }
    function replyToComment(rootId, commentId, username) {
        const formContainer = document.getElementById(`reply-form-container-${rootId}`);
        const inputBody = document.getElementById(`reply-input-${rootId}`);
        const inputParent = formContainer ? formContainer.querySelector(`input[name="parent_id"]`) : null;
        if (!formContainer || !inputBody) return;
        formContainer.classList.remove('hidden');
        inputBody.value = `@${username} `;
        if (inputParent) inputParent.value = commentId;
        inputBody.focus();
    }
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
                const postCounter = document.getElementById(`post-comment-count-${postId}`);
                if(postCounter) postCounter.innerText = parseInt(postCounter.innerText) + 1;
                if (rootId) {
                    const replyContainer = document.getElementById(`replies-container-${rootId}`);
                    if(replyContainer) {
                        replyContainer.classList.remove('hidden');
                        replyContainer.insertAdjacentHTML('afterbegin', data.html);
                    }
                    const formContainer = document.getElementById(`reply-form-container-${rootId}`);
                    if(formContainer) formContainer.classList.add('hidden');
                } else {
                    const mainContainer = document.getElementById(`comments-container-${postId}`);
                    if(mainContainer) {
                        mainContainer.insertAdjacentHTML('afterbegin', data.html);
                        mainContainer.scrollTop = 0;
                    }
                }
            } else { alert('Gagal: ' + data.message); }
        } catch (error) { console.error(error); alert('Error'); btn.innerText = originalText; btn.disabled = false; }
    }
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
                const postCounter = document.getElementById(`post-comment-count-${data.post_id}`);
                if(postCounter) postCounter.innerText = Math.max(0, parseInt(postCounter.innerText) - 1);
                if (data.parent_id) {
                    const btnContainer = document.getElementById(`reply-btn-container-${data.parent_id}`);
                    const countText = document.getElementById(`reply-count-text-${data.parent_id}`);
                    if (data.remaining_replies <= 0) {
                        if(btnContainer) btnContainer.classList.add('hidden');
                    } else {
                        if(countText) {
                            const mode = countText.innerText.includes('Hide') ? 'Hide' : 'View';
                            countText.innerText = `${mode} ${data.remaining_replies} replies`;
                        }
                    }
                }
            }
        } catch (error) { console.error(error); }
    }
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