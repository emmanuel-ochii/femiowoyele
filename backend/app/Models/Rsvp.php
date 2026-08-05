<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'attending',
        'guests',
        'note',
        'event_slug',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'attending' => 'boolean',
            'guests' => 'integer',
        ];
    }
}
