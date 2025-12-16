<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($topic) {
            $topic->name = $topic->name ? trim($topic->name) : null;
        });
        
        static::updating(function ($topic) {
            if (isset($topic->name)) {
                $topic->name = trim($topic->name);
            }
        });
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}