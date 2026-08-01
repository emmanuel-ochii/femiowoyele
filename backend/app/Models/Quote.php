<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'source', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
