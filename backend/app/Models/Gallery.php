<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function mediaItems(): BelongsToMany
    {
        return $this->belongsToMany(MediaItem::class, 'gallery_items')->withPivot('order')->withTimestamps();
    }
}
