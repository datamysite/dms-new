<?php
// File: app/Models/RedirectRevision.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedirectRevision extends Model
{
    protected $table = 'redirect_revisions';

    public $timestamps = false; // only created_at, set by the database default

    protected $fillable = ['redirect_id', 'from_path', 'action', 'previous', 'current', 'actor', 'created_at'];

    protected $casts = [
        'previous'   => 'array',
        'current'    => 'array',
        'created_at' => 'datetime',
    ];

    public static function record(string $action, ?Redirect $redirect, string $fromPath, ?array $previous, ?array $current, ?string $actor): self
    {
        return static::create([
            'redirect_id' => $redirect?->id,
            'from_path'   => $fromPath,
            'action'      => $action,
            'previous'    => $previous,
            'current'     => $current,
            'actor'       => $actor,
            'created_at'  => now(),
        ]);
    }
}
