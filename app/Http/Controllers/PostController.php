<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Post;
use Illuminate\Http\Request;
// 1. Pastikan Import Facades ini ada
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
{
    $posts = Post::with([
        'user',
        'comments.user',
        'comments.replies.user'
    ])
    ->latest()
    ->paginate(10);

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

        // ============================================================
        // PERBAIKAN DI SINI:
        // Ganti auth()->id() menjadi Auth::id() agar error merah hilang
        // ============================================================
        $post = Post::create([
            'user_id' => Auth::id(), 
            'image' => $path,
            'caption' => $request->caption,
        ]);

        // --- LOGIKA TOPIC/HASHTAG ---
        if ($request->caption) {
            preg_match_all('/#(\w+)/', $request->caption, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $tagName) {
                    $topic = Topic::firstOrCreate(['name' => $tagName]);
                    $post->topics()->attach($topic->id);
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'Post created.');
    }

    public function show(Post $post)
    {
        $post->load('user');
        return view('posts.show', compact('post'));
    }

    public function destroy(Post $post)
{
    if ($post->user_id !== Auth::id()) {
        abort(403);
    }

    $post->delete();

    return response()->json(['success' => true]);
}


    public function explore()
    {
        $posts = Post::latest()->take(20)->get();
        return view('posts.explore', compact('posts'));
    }
}