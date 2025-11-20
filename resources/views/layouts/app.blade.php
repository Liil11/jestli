<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jestli</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-darkDeep text-white">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-56 bg-grayComp text-white border-r shadow-md flex flex-col">
        <img src="{{ asset('image/logo-jestli.png') }}" 
    class="w-24 mx-auto"
       alt="Decorative Image">

        <nav class="space-y-1 flex-1 mt-4 text-center text-lg ">
            <a href="{{ route('dashboard') }}" class="py-3 px-3 hover:bg-tealPrimary flex gap-4 justify-center pr-16"><x-icons.home class="w-[37px] h-[37px] text-white " /><p class="pt-1">Home</p></a>
            <a href="{{ route('posts.create') }}" class="py-3 px-3 hover:bg-tealPrimary flex gap-[20px] justify-center pr-16"><x-icons.add class="w-[39px] h-[39px] text-white " /><p class="pt-1 pr-2">Post</p></a>
            <a href="{{ route('explore') }}" class="py-3 px-3 hover:bg-tealPrimary flex gap-6 justify-center pr-16"><x-icons.topic class="w-[30px] h-[30px] text-white " /><p class="">Topic</p></a>
            <a href="{{ route('profile.show', auth()->user()->id) }}" class=" py-3 px-5 hover:bg-tealPrimary flex gap-6 justify-center pr-16"><x-icons.profile class="w-[30px] h-[30px] text-white " /><p class="pt-1">Profile</p></a>
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 font-semibold hover:text-red-800">
                Logout
            </button>
        </form>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-y-auto">

        <!-- Top bar -->
        <header class="bg-grayComp text-white shadow px-6 py-4 flex items-center justify-between border-b">
            <!-- Search -->
            <form action="{{ route('search') }}" method="GET" class="w-1/2">
                <input type="text" name="q" placeholder="Search posts or users..."
                    class="w-2/3 bg-formcolor rounded-lg px-4 py-2 border-0 focus:border-tealPrimary focus:ring-tealPrimary">
            </form>

            <!-- Right section -->
            <div class="flex items-center space-x-4">
                <div class="flex gap-8 mr-20 ">
                    <a href=""
                    class="relative inline-block
                        after:content-[''] after:absolute after:left-0 after:bottom-0
                        after:h-0.5 after:bg-tealPrimary after:w-full
                        after:scale-x-0 after:origin-right
                        after:transition-transform after:duration-300
                        hover:after:origin-left hover:after:scale-x-100">For You
                    </a>

                    <a href=""
                    class="relative inline-block
                        after:content-[''] after:absolute after:left-0 after:bottom-0
                        after:h-0.5 after:bg-tealPrimary after:w-full
                        after:scale-x-0 after:origin-right
                        after:transition-transform after:duration-300
                        hover:after:origin-left hover:after:scale-x-100">Trending
                    </a>

                    <a href=""
                    class="relative inline-block
                        after:content-[''] after:absolute after:left-0 after:bottom-0
                        after:h-0.5 after:bg-tealPrimary after:w-full
                        after:scale-x-0 after:origin-right
                        after:transition-transform after:duration-300
                        hover:after:origin-left hover:after:scale-x-100">Following
                    </a>

                </div>
                
                <a href="{{ route('profile.show', auth()->user()?->id) }}" class="font-semibold hover:text-blue-600">
                    {{ auth()->user()?->name }}
                </a>
                <a href="{{ route('profile.edit', auth()->user()?->id) }}" class="text-gray-500 hover:text-blue-500"></a>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
