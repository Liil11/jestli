<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    public function toggleLike(Post $post)
    {
        $user = Auth::user();
        
        if ($post->isLikedByAuthUser()) {
            $post->likedByUsers()->detach($user->id);
            $post->decrement('likes_count');
            $status = 'unliked';
        } else {
            $post->likedByUsers()->attach($user->id);
            $post->increment('likes_count');
            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'count' => $post->likes_count
        ]);
    }

    public function toggleUpvote(Post $post)
    {
        $user = Auth::user();

        if ($post->isUpvotedByAuthUser()) {
            $post->upvotedByUsers()->detach($user->id);
            $post->decrement('upvotes_count');
            $status = 'removed';
        } else {
            $post->upvotedByUsers()->attach($user->id);
            $post->increment('upvotes_count');
            $status = 'upvoted';
        }

        return response()->json([
            'status' => $status,
            'count' => $post->upvotes_count
        ]);
    }
}