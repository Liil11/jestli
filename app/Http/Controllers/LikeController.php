<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle($id)
    {
        $post = Post::findOrFail($id);

        $existing = Like::where('post_id', $id)
                        ->where('user_id', auth()->id())
                        ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->id()
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $post->likes()->count()
        ]);
    }
}
