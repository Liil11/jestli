@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-6">
    
    {{-- Image/Logo Container --}}
    <div class="bg-grayComp rounded-lg p-12 mb-8 flex justify-center items-center shadow-sm">
        <img src="{{ asset('image/logo-jestli.png') }}" alt="Jestli Logo" class="w-48 opacity-90">
    </div>

    {{-- Text Content --}}
    <div class="space-y-4">
        <h1 class="text-2xl font-bold text-white mb-4">About Us</h1>
        
        <p class="text-gray-300 leading-relaxed text-justify">
            Jestli is a community-driven social platform built specifically for sharing and discovering memes. It provides a space where users can express creativity through humor, connect with others who share the same interests, and engage in lively interactions. On Jestli, users can post their own memes, explore content from different creators, follow accounts they enjoy, and participate in discussions through likes and comments. The platform is designed to foster an active and inclusive community where memes are not just entertainment, but a way to communicate ideas, emotions, and trends in a simple and fun format.

        </p>
    </div>

</div>
@endsection