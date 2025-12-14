<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($topic) {
            $topic->slug = $topic->slug ?? \Str::slug($topic->name);
        });
        
        static::updating(function ($topic) {
            if (!$topic->slug) {
                $topic->slug = \Str::slug($topic->name);
            }
        });
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}