<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pillar extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'subtitle', 'description', 'order'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('title');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class)->latest('published_at');
    }

    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class)->latest('published_at');
    }
}
