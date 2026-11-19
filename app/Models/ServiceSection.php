<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSection extends Model
{
    protected $table = 'service_sections';

    protected $fillable = [
        'service_id', 'area', 'section_key', 'type', 'title', 'content', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}
