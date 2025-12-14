@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto min-h-screen bg-feedsbg border-x border-gray-800 pb-20">
    
    {{-- HEADER: Tombol Back & Judul --}}
    <div class="sticky top-[72px] z-20 bg-[#121212]/95 backdrop-blur-md border-b border-gray-800 px-4 py-3 flex items-center gap-4">
        <a href="{{ url()->previous() }}" class="p-2 rounded-full hover:bg-gray-800 text-gray-300 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-lg font-bold text-white">Post</h2>
    </div>

    {{-- KONTEN UTAMA: Mengambil dari Partial --}}
    <div class="mt-0">
        @include('partials.post-item', ['post' => $post])
    </div>

</div>

{{-- SCRIPT: Kebutuhan Interaksi (Like, Comment, Reply) --}}
{{-- PENTING: Jika kamu belum memindahkan fungsi JS di index.blade.php ke file global (public/js/app.js), 
     maka kamu HARUS menyertakan script ini agar tombol-tombol di post-item berfungsi di halaman ini. --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // FITUR TAMBAHAN: Otomatis buka kolom komentar saat masuk halaman detail
        const commentSection = document.getElementById("comments-section-{{ $post->id }}");
        if(commentSection) {
            commentSection.classList.remove('hidden');
        }
    });

    // --- SETUP VARIABLES ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    // --- COPAS FUNGSI DARI INDEX.BLADE.PHP ---
    // Agar tombol Like, Reply, dan Comment berfungsi di halaman detail ini.
    
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
                    // Update text View/Hide
                    span.innerText = span.innerText.replace(container.classList.contains('hidden') ? 'Hide' : 'View', container.classList.contains('hidden') ? 'View' : 'Hide');
                }
            }
        }
    }

    function replyToComment(rootId, commentId, username) {
        const formContainer = document.getElementById(`reply-form-container-${rootId}`);
        const inputBody = document.getElementById(`reply-input-${rootId}`);
        // Cari input hidden parent_id
        const inputParent = formContainer ? formContainer.querySelector(`input[name="parent_id"]`) : null;

        if (!formContainer || !inputBody) return;

        formContainer.classList.remove('hidden');
        inputBody.value = `@${username} `;
        
        if (inputParent) inputParent.value = commentId;
        
        inputBody.focus();
    }

    // AJAX SUBMIT (Sama persis dengan Index/Profile)
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
                
                // Update counter
                const postCounter = document.getElementById(`post-comment-count-${postId}`);
                if(postCounter) postCounter.innerText = parseInt(postCounter.innerText) + 1;

                if (rootId) {
                    // Logic Reply
                    const replyContainer = document.getElementById(`replies-container-${rootId}`);
                    const btnContainer = document.getElementById(`reply-btn-container-${rootId}`);
                    const countText = document.getElementById(`reply-count-text-${rootId}`);
                    const formContainer = document.getElementById(`reply-form-container-${rootId}`);

                    if(replyContainer) {
                        replyContainer.classList.remove('hidden');
                        replyContainer.insertAdjacentHTML('afterbegin', data.html); 
                    }
                    if(btnContainer) btnContainer.classList.remove('hidden');
                    if(countText) {
                        const match = countText.innerText.match(/\d+/);
                        const currentCount = match ? parseInt(match[0]) : 0;
                        countText.innerText = `Hide ${currentCount + 1} replies`; 
                    }
                    if(formContainer) formContainer.classList.add('hidden');
                } else {
                    // Logic Main Comment
                    const mainContainer = document.getElementById(`comments-container-${postId}`);
                    if(mainContainer) {
                        mainContainer.insertAdjacentHTML('afterbegin', data.html);
                        mainContainer.scrollTop = 0;
                    }
                }
            } else { alert('Gagal: ' + data.message); }
        } catch (error) { console.error(error); alert('Error'); btn.innerText = originalText; btn.disabled = false; }
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
        try {
            const response = await fetch(`/comments/${commentId}/like`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await response.json();
            countSpan.innerText = data.count;
            if (data.count > 0) countSpan.classList.remove('hidden'); else countSpan.classList.add('hidden');
            if (data.status === 'liked') {
                btn.classList.add('text-pink-600'); btn.classList.remove('text-gray-500');
            } else {
                btn.classList.remove('text-pink-600'); btn.classList.add('text-gray-500');
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
                
                // Logic update UI jika nested reply dihapus (opsional, sesuaikan dgn index.blade.php)
            }
        } catch (error) { console.error(error); }
    }
</script>
@endsection