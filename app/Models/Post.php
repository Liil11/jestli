<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'image', 'caption'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // accessor to full public URL
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy($user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

}
