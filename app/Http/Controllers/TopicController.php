<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Post;

class TopicController extends Controller
{
   public function show($name)
{
    // Use findOrFail to catch the error
    $topic = Topic::where('name', $name)->firstOrFail();
    
    $posts = $topic->posts()->with('user')->latest()->paginate(10);
    
    return view('topics.show', compact('topic', 'posts'));
}
}