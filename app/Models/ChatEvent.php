<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatEvent extends Model
{
    protected $fillable = ['sid', 'event', 'meta', 'conversation'];

    protected $casts = [
        'meta' => 'array',
    ];
}
