<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSectionRevision extends Model
{
    const UPDATED_AT = null;

    protected $table = 'service_section_revisions';

    protected $fillable = ['section_id', 'service_id', 'action', 'snapshot'];

    protected $casts = ['snapshot' => 'array'];
}
