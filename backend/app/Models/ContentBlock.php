<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'body', 'context', 'meta', 'order'];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }
}
