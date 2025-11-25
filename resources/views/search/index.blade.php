@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Search results for "{{ $q }}"</h1>
<p class="text">{{ $posts->count() }} posts and {{ $users->count() }} users found.</p>
@endsection
