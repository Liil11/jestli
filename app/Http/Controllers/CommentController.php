<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:255'
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        // Load user relationship for response
        $comment->load('user:id,name,avatar');

        return response()->json([
            'comment' => $comment,
            'user' => $comment->user
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }
}