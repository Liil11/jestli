<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'body',
        'parent_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->orderBy('created_at', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'comment_likes')->withTimestamps();
    }

    public function isLikedByAuthUser()
    {
        if (!Auth::check()) return false;
        return $this->likedByUsers->contains(Auth::id());
    }
}
