@extends('layouts.app')

@section('content')
<div class="min-h-screen text-white p-6">
    
    <div class="max-w-2xl mx-auto mt-4">
        
        <h1 class="text-3xl font-bold mb-8">Topics</h1>

        <div class="flex flex-col gap-4">

            @forelse($topics ?? [] as $topic)
                <a href="{{ route('topics.show', $topic->name) }}" 
                class="group block w-full border border-gray-600 rounded-lg p-5 text-lg font-medium text-gray-200 transition-all duration-200 hover:bg-white/10 hover:border-white hover:text-white">
                    
                    
                    #{{ $topic->name ?? $topic }}
                </a>
            @empty
                <a href="#" class="block w-full border border-gray-600 rounded-lg p-5 text-lg font-medium text-gray-200 hover:border-white hover:bg-white/10">#Lorem</a>
                <a href="#" class="block w-full border border-gray-600 rounded-lg p-5 text-lg font-medium text-gray-200 hover:border-white hover:bg-white/10">#Meme</a>
                <a href="#" class="block w-full border border-gray-600 rounded-lg p-5 text-lg font-medium text-gray-200 hover:border-white hover:bg-white/10">#Padel</a>
                <a href="#" class="block w-full border border-gray-600 rounded-lg p-5 text-lg font-medium text-gray-200 hover:border-white hover:bg-white/10">#Coding</a>
                <a href="#" class="block w-full border border-gray-600 rounded-lg p-5 text-lg font-medium text-gray-200 hover:border-white hover:bg-white/10">#Laravel</a>
            @endforelse
        </div>

    </div>
</div>
@endsection