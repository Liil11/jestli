@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-teal-700 mb-2">
            #{{ $topic->name }}
        </h1>
        {{-- <p class="text-gray-600">{{ $posts->count() }} posts found</p> --}}
    </div>

    @if($posts->count() > 0)
        <div class="space-y-6">
            @foreach($posts as $post)
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
            @endforeach
        </div>
        {{ $posts->links() }}
    @else
        <p>No posts found for this topic.</p>
    @endif
    <Script>
    
    </Script>
</div>
@endsection