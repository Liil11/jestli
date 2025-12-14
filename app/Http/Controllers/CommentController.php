<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $request->validate([
            'body' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'user_id'   => Auth::id(),
            'post_id'   => $post->id,
            'body'      => $request->body,
            'parent_id' => $request->parent_id
        ]);

        // 🔥 FIX UTAMA: SIMPAN KE DATABASE
        $post->increment('comments_count');

        $comment->load(['user', 'replies.user']);

        $html = view('partials.comment-item', [
            'comment' => $comment,
            'root_id' => $comment->parent_id ?? $comment->id
        ])->render();

        return response()->json([
            'status'    => 'success',
            'html'      => $html,
            'parent_id' => $comment->parent_id,
            'post_id'   => $post->id
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $postId   = $comment->post_id;
        $parentId = $comment->parent_id;

        $remainingReplies = 0;
        if ($parentId) {
            $remainingReplies = Comment::where('parent_id', $parentId)->count() - 1;
        }

        $comment->delete();

        // 🔥 FIX: SYNC COUNTER
        Post::where('id', $postId)->decrement('comments_count');

        return response()->json([
            'success' => true,
            'post_id' => $postId,
            'parent_id' => $parentId,
            'remaining_replies' => max(0, $remainingReplies)
        ]);
    }

    public function toggleLike(Comment $comment)
    {
        $user = Auth::user();

        if ($comment->likedByUsers()->where('user_id', $user->id)->exists()) {
            $comment->likedByUsers()->detach($user->id);
            $status = 'unliked';
        } else {
            $comment->likedByUsers()->attach($user->id);
            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'count'  => $comment->likedByUsers()->count()
        ]);
    }
}
