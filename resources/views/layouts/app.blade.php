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

    <!-- Sidebar -->
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
                <a href="{{ route('posts.create') }}" class="py-3 px-3 hover:bg-tealPrimary flex gap-[20px] justify-center pr-16">
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
                <a href="" class="hover:bg-tealPrimary py-2 px-3 pl-11">About Us</a>
                <a href="" class="hover:bg-tealPrimary py-2 px-3 pl-11">Contact</a>
            </nav>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 font-semibold hover:text-red-800">
                Logout
            </button>
        </form>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">

        <!-- Top bar -->
        <header class="bg-grayComp text-white shadow px-6 py-4 flex items-center justify-between border-b border-grayShadow sticky top-0 z-10">
            <!-- Search -->
            <form action="{{ route('search') }}" method="GET" class="w-1/2">
                <input type="text" name="q" placeholder="Search meme or users..."
                       class="placeholder-formtext w-2/3 bg-formcolor rounded-lg px-4 py-2 border-0 focus:border-tealPrimary focus:ring-tealPrimary">
            </form>

            <!-- Right section -->
            <div class="flex items-center space-x-4">
                <div class="flex gap-8 mr-20">
                    <a href=""
                       class="relative inline-block
                              after:content-[''] after:absolute after:left-0 after:bottom-0
                              after:h-0.5 after:bg-tealPrimary after:w-full
                              after:scale-x-0 after:origin-right
                              after:transition-transform after:duration-300
                              hover:after:origin-left hover:after:scale-x-100">
                        For You
                    </a>

                    <a href=""
                       class="relative inline-block
                              after:content-[''] after:absolute after:left-0 after:bottom-0
                              after:h-0.5 after:bg-tealPrimary after:w-full
                              after:scale-x-0 after:origin-right
                              after:transition-transform after:duration-300
                              hover:after:origin-left hover:after:scale-x-100">
                        Trending
                    </a>

                    <a href=""
                       class="relative inline-block
                              after:content-[''] after:absolute after:left-0 after:bottom-0
                              after:h-0.5 after:bg-tealPrimary after:w-full
                              after:scale-x-0 after:origin-right
                              after:transition-transform after:duration-300
                              hover:after:origin-left hover:after:scale-x-100">
                        Following
                    </a>
                </div>

                <a href="{{ route('profile.show', auth()->user()?->id) }}" class="font-semibold hover:text-blue-600">
                    {{ auth()->user()?->name }}
                </a>
                <a href="{{ route('profile.edit', auth()->user()?->id) }}" class="text-gray-500 hover:text-blue-500"></a>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1">
            @yield('content')
        </main>

    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".like-btn").forEach(btn => {

        btn.addEventListener("click", function () {
            const postId = this.dataset.post;

            fetch(`/posts/${postId}/like`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                this.querySelector(".icon").textContent = data.liked ? "❤️" : "🤍";
                this.querySelector(".count").textContent = data.count;
            })
            .catch(err => console.error(err));
        });

    });
});
</script>

</body>
</html>
