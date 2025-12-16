@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-teal-600 mb-2">
            Search results for "{{ $q }}"
        </h1>
    </div>

    @if($posts->count() > 0 || $users->count() > 0)
        <div class="space-y-8">
            <div>
                @if($users->count() > 0)
                    <h2 class="text-xl font-semibold text-cyan-800 mb-4 border-b pb-2">
                        Users ({{ $users->count() }})
                    </h2>   
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($users as $user)
                            <div class="bg-feedsbg shadow-sm p-4 hover:shadow-md transition-shadow">
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
                                        <p class="text-white-500 text-sm">joined {{ $user->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($posts->count() > 0)
                    <h2 class="text-xl font-semibold text-tealSecond mb-4 border-b pb-2">
                        Posts ({{ $posts->count() }})
                    </h2>
                    
                        @foreach($posts as $post)
                            @include('partials.post-item', ['post' => $post])
                        @endforeach
                    
                @endif
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-tealPrimary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-400">No results found</h3>
            <p class="mt-1 text-sm text-gray-400">Try adjusting your search query.</p>
        </div>
    @endif

    
</div>
@endsection
