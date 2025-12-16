<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $fillable = ['user_id', 'caption', 'image', 'likes_count', 'upvotes_count', 'comments_count'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class)->orderBy('likes_count', 'desc');
    }

    public function topics() {
        return $this->belongsToMany(Topic::class);
    }

    public function likedByUsers() {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id');
    }

    public function upvotedByUsers() {
        return $this->belongsToMany(User::class, 'upvotes', 'post_id', 'user_id');
    }

    public function isLikedByAuthUser() {
        return $this->likedByUsers()->where('user_id', Auth::id())->exists();
    }

    public function isUpvotedByAuthUser() {
        return $this->upvotedByUsers()->where('user_id', Auth::id())->exists();
    }

    public function getFormattedCaptionAttribute()
    {
        $text = e($this->caption);

        return preg_replace(
            '/@([a-zA-Z0-9_]+)/', 
            '<a href="/profile/$1" class="text-teal-400 hover:underline font-bold">@$1</a>', 
            $text
        );
    }
}