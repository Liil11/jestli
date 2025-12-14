<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['name'];

    public function posts() {
        // Jika diurutkan berdasarkan upvote (Logic Topic Teratas)
        return $this->belongsToMany(Post::class)->orderBy('upvotes_count', 'desc');
    }
     protected static function booted()
    {
        static::saving(function ($topic) {
            $topic->name = strtolower($topic->name);
        });
    }
}