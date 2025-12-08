<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->paginate(10);
        return view('posts.index', compact('posts')); 
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:8192',
            'caption' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('image')->store('posts', 'public');

        $post = Post::create([
            'user_id' => auth()->id(),
            'image' => $path,
            'caption' => $request->caption,
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Post created.');
    }

    public function show(Post $post)
    {
        $post->load('user');
        return view('posts.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post); // we'll create a policy or simply check user
        // delete image file
        \Storage::disk('public')->delete($post->image);
        $post->delete();
        return redirect()->route('dashboard')->with('success', 'Post deleted.');
    }
    public function explore()
    {
        $posts = \App\Models\Post::latest()->take(20)->get();
        return view('posts.explore', compact('posts'));
    }

}
