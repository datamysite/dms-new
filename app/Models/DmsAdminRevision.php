<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One saved "before" copy of a file the admin API changed
 * (a Blade template, routes/redirects.php or a deleted meta row).
 */
class DmsAdminRevision extends Model
{
    protected $table = 'dms_admin_revisions';

    protected $fillable = ['kind', 'target', 'content', 'note', 'actor'];
}
