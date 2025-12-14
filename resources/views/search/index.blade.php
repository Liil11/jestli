@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-teal-600 mb-2">
            Search results for "{{ $q }}"
        </h1>
        {{-- <p class="text-white-600">
            Found {{ $posts->count() }} post{{ $posts->count() !== 1 ? 's' : '' }} 
            and {{ $users->count() }} user{{ $users->count() !== 1 ? 's' : '' }}
        </p> --}}
    </div>

    
    @if($posts->count() > 0 || $users->count() > 0)
        <div class="space-y-8">
            <!-- Users Section -->
            @if($users->count() > 0)
                <div>
                    <h2 class="text-xl font-semibold text-cyan-800 mb-4 border-b pb-2">
                        Users ({{ $users->count() }})
                    </h2>   
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($users as $user)
                            <div class="bg-feedsbg shadow-sm  p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center space-x-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" class="rounded-full w-12 h-12 object-cover flex" alt="Avatar">
                                @else
                                    <div class="w-12 h-12 bg-teal-600 rounded-full flex items-center justify-center">
                                        <span class="text-white-600 font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                    <div>
                                        <a href="{{ route('profile.show', $user->id) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                            {{ $user->name }}
                                        </a>
                                        <p class="text-white-500 text-sm" value="">joined {{ $user->created_at->diffForHumans()}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <!-- Posts Section -->
            @if($posts->count() > 0)
                <div>
                    <h2 class="text-xl font-semibold text-cyan-800 mb-4 border-b pb-2">
                        Posts ({{ $posts->count() }})
                    </h2>
                    <div class="space-y-3">
                        @foreach($posts as $post)
                            <div id="content-posts" class="tab-content px-4 md:px-0">
                        @forelse($posts as $post)
                            @include('partials.post-item', ['post' => $post])
                        @empty
                            <div class="text-center text-gray-500 py-10">No posts available.</div>
                            @endforelse
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

           
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
            <p class="mt-1 text-sm text-gray-500">
                Try adjusting your search query.
            </p>
        </div>
    @endif

    <!-- Back to search button -->
    <div class="mt-8">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            ← Back to Home
        </a>
    </div>
</div>
@endsection