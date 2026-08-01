<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'description',
        'cover_image_path',
        'is_featured',
        'order',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean'];
    }
}
