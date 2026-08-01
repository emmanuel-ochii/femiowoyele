<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['pillar_id', 'title', 'slug', 'summary', 'content'];

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }
}
