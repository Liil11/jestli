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
        return $this->hasMany(Comment::class)->orderBy('likes_count', 'desc'); // Komen populer di atas
    }

    public function topics() {
        return $this->belongsToMany(Topic::class);
    }

    // Relasi User yang me-like post ini
    public function likedByUsers() {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id');
    }

    public function upvotedByUsers() {
        return $this->belongsToMany(User::class, 'upvotes', 'post_id', 'user_id');
    }

    // Helper untuk cek apakah user login sudah like/upvote
    public function isLikedByAuthUser() {
        return $this->likedByUsers()->where('user_id', Auth::id())->exists();
    }

    public function isUpvotedByAuthUser() {
        return $this->upvotedByUsers()->where('user_id', Auth::id())->exists();
    }

    public function getFormattedCaptionAttribute()
    {
        // 1. Escape teks asli agar aman dari XSS (script jahat)
        $text = e($this->caption);

        // 2. Ubah @username menjadi link
        return preg_replace(
            '/@([a-zA-Z0-9_]+)/', 
            '<a href="/profile/$1" class="text-teal-400 hover:underline font-bold">@$1</a>', 
            $text
        );
    }
}