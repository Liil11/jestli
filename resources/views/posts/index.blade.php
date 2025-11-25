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
                <button class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.like class="w-full h-full" />
                    </div>
                    <span>80</span>
                </button>
                <button class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <x-icons.comment class="w-full h-full" />
                    </div>
                    <span>80</span>
                </button>
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
