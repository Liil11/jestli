<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $post = Post::create([
            'user_id' => Auth::id(), 
            'image' => $path,
            'caption' => $request->caption,
        ]);

        if ($request->caption) {
            preg_match_all('/#([a-zA-Z0-9_-]+)/', $request->caption, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $tagName) {
                    $topic = Topic::firstOrCreate(['name' => strtolower($tagName)]);
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
        if (Auth::id() !== $post->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($post->image) {
            \Illuminate\Support\Facades\Storage::delete($post->image);
        }

        $post->delete();

        return response()->json(['success' => true, 'message' => 'Post deleted successfully']);
    }

    public function explore()
    {
        $topics = Topic::withCount('posts')->orderByDesc('posts_count')->get();
        
        return view('posts.explore', compact('topics'));
    }
}
