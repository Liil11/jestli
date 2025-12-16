<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jestli</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>



<body class="bg-darkDeep text-white overflow-y-scroll">

<div class="flex min-h-screen">

    <aside class="w-56 bg-grayComp text-white border-r border-grayShadow shadow-md flex flex-col sticky top-0 h-screen overflow-y-auto">
        <img src="{{ asset('image/logo-jestli.png') }}" 
             class="w-24 mx-auto"
             alt="Decorative Image">

        <nav class="space-y-1 mt-4 text-center text-[17px]">
            <div class="flex flex-col">
                <a href="{{ route('dashboard') }}" class="py-3 pb-4 px-3 hover:bg-tealPrimary flex gap-5 justify-center pr-16">
                    <x-icons.home class="w-[37px] h-[37px] text-white" />
                    <p class="pt-2">Home</p>
                </a>
                
                <a href="#" id="openPostModalSidebar" class="py-3 px-3 hover:bg-tealPrimary flex gap-[20px] justify-center pr-16 cursor-pointer">
                    <x-icons.add class="w-[39px] h-[39px] text-white" />
                    <p class="pt-1 pr-2">Post</p>
                </a>

                <a href="{{ route('explore') }}" class="py-4 px-3 hover:bg-tealPrimary flex gap-6 justify-center pr-16">
                    <x-icons.topic class="w-[30px] h-[30px] text-white" />
                    <p>Topic</p>
                </a>
                <a href="{{ route('profile.show', auth()->user()->id) }}" class="py-4 px-5 hover:bg-tealPrimary flex gap-6 justify-center pr-16">
                    <x-icons.profile class="w-[30px] h-[30px] text-white" />
                    <p class="pt-1">Profile</p>
                </a>
            </div>
        </nav>

        <div class="border-t flex flex-col gap-2 pt-3 mt-5 border-grayShadow">
            <h1 class="font-medium text-[18.5px] pl-4">Information</h1>
            <nav class="flex flex-col text-left">
                <a href="{{ route('about') }}" class="hover:bg-tealPrimary py-2 px-3 pl-11">About Us</a>
                <a href="{{ route('contact') }}" class="hover:bg-tealPrimary py-2 px-3 pl-11">Contact</a>
            </nav>
        </div>

    </aside>

    <div class="flex-1 flex flex-col">

        <header class="bg-grayComp text-white shadow px-6 py-4 flex items-center justify-between border-b border-grayShadow sticky top-0 z-10">
            <form action="{{ route('search') }}" method="GET" class="w-1/2">
                <input type="text" name="q" placeholder="Search meme or users..."
                       class="placeholder-formtext w-2/3 bg-formcolor rounded-lg px-4 py-2 border-0 focus:border-tealPrimary focus:ring-tealPrimary">
            </form>

            <div class="flex items-center space-x-4">
                <div class="flex gap-8 mr-20">
                    <a href="{{ route('dashboard') }}"
                       class="relative inline-block
                              after:content-[''] after:absolute after:left-0 after:bottom-0
                              after:h-0.5 after:bg-tealPrimary after:w-full
                              after:scale-x-0 after:origin-right
                              after:transition-transform after:duration-300
                              hover:after:origin-left hover:after:scale-x-100">
                        For You
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="relative inline-block
                              after:content-[''] after:absolute after:left-0 after:bottom-0
                              after:h-0.5 after:bg-tealPrimary after:w-full
                              after:scale-x-0 after:origin-right
                              after:transition-transform after:duration-300
                              hover:after:origin-left hover:after:scale-x-100">
                        Trending
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="relative inline-block
                              after:content-[''] after:absolute after:left-0 after:bottom-0
                              after:h-0.5 after:bg-tealPrimary after:w-full
                              after:scale-x-0 after:origin-right
                              after:transition-transform after:duration-300
                              hover:after:origin-left hover:after:scale-x-100">
                        Following
                    </a>
                </div>

                <script src="//unpkg.com/alpinejs" defer></script>

<div class="flex items-center gap-3">
    
    <div x-data="{ open: false }" class="relative">
        
        <button @click="open = !open" type="button" class="flex items-center focus:outline-none transition hover:opacity-80">
            @if(auth()->user()->avatar)
                <img src="{{ Storage::url(auth()->user()->avatar) }}" class="rounded-full w-12 h-12 object-cover flex" alt="Avatar">
            @else
                <div class="w-12 h-12 bg-teal-600 rounded-full flex items-center justify-center">
                    <span class="text-white-600 font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                 </div>
            @endif
        </button>

        <div x-show="open" 
             @click.outside="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-48 bg-grayComp rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5"
             style="display: none;">
            
            <a href="{{ route('profile.show', auth()->user()->id) }}" class="block px-4 py-2 text-sm hover:bg-tealPrimary">
                View Profile
            </a>

            <div class="border-t border-grayShadow"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-600 hover:text-white">
                    Logout
                </button>
            </form>

        </div>
    </div>

</div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
        
    </div>
</div>


@include('posts.create')

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // toggle view Comment Section
    function toggleComments(postId) {
        const section = document.getElementById(`comments-section-${postId}`);
        section.classList.toggle('hidden');
    }

    // toggle like post
    async function toggleLike(postId) {
        const btn = document.getElementById(`like-btn-${postId}`);
        const countSpan = document.getElementById(`like-count-${postId}`);
        const icon = btn.querySelector('svg');

        try {
            const response = await fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await response.json();

            countSpan.innerText = data.count;
            if (data.status === 'liked') {
                btn.classList.add('text-pink-600');
                btn.classList.remove('hover:text-pink-600');
                icon.classList.add('fill-current');
            } else {
                btn.classList.remove('text-pink-600');
                btn.classList.add('hover:text-pink-600');
                icon.classList.remove('fill-current');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // ini toggle upvote post
    async function toggleUpvote(postId) {
        const btn = document.getElementById(`upvote-btn-${postId}`);
        const countSpan = document.getElementById(`upvote-count-${postId}`);
        const icon = btn.querySelector('svg');

        try {
            const response = await fetch(`/posts/${postId}/upvote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await response.json();

            countSpan.innerText = data.count;
            if (data.status === 'upvoted') {
                btn.classList.add('text-green-500');
                btn.classList.remove('hover:text-green-500');
                icon.classList.add('fill-current');
            } else {
                btn.classList.remove('text-green-500');
                btn.classList.add('hover:text-green-500');
                icon.classList.remove('fill-current');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/*SUBMIT COMMENT / REPLY (AJAX)*/
function submitCommentAjax(postId, parentId = null) {
    let input = parentId
        ? document.getElementById(`reply-input-${parentId}`)
        : document.getElementById(`comment-input-${postId}`);

    if (!input || input.value.trim() === '') {
        alert('Komentar tidak boleh kosong');
        return;
    }

    fetch(`/posts/${postId}/comments`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            body: input.value,
            parent_id: parentId
        })
    })
    .then(res => {
        if (!res.ok) throw new Error('Gagal kirim komentar');
        return res.json();
    })
    .then(() => {
        location.reload();
    })
    .catch(err => {
        alert(err.message);
    });
}

/*LIKE COMMENT */
function toggleCommentLike(commentId) {
    fetch(`/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        const btn = document.getElementById(`comment-like-btn-${commentId}`);
        const count = document.getElementById(`comment-like-count-${commentId}`);

        if (data.status === 'liked') {
            btn.classList.add('text-pink-600');
            btn.classList.remove('text-gray-500','text-gray-600');
            count.classList.remove('hidden');
        } else {
            btn.classList.remove('text-pink-600');
        }

        count.innerText = data.count;
        if (data.count == 0) count.classList.add('hidden');
    });
}

/*DELETE COMMENT*/
function deleteComment(commentId) {
    if (!confirm('Hapus komentar?')) return;

    fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(() => {
        const row = document.getElementById(`comment-row-${commentId}`);
        if (row) row.remove();
    });
}
</script>

</body>
</html>