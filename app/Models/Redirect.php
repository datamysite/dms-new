<?php
// File: app/Models/Redirect.php

namespace App\Models;

use App\Support\Redirects\RedirectMap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $table = 'redirects';

    protected $fillable = [
        'from_path', 'to_url', 'status_code', 'is_active', 'note', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'is_active'   => 'boolean',
        'hits'        => 'integer',
        'last_hit_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Any change clears the cached map, so the site picks it up on the next request.
        static::saved(fn () => RedirectMap::forget());
        static::deleted(fn () => RedirectMap::forget());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** The fields stored in a revision snapshot. */
    public function snapshot(): array
    {
        return [
            'from_path'   => $this->from_path,
            'to_url'      => $this->to_url,
            'status_code' => $this->status_code,
            'is_active'   => (bool) $this->is_active,
            'note'        => $this->note,
        ];
    }
}
