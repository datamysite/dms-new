<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Author;
use App\Models\BlogTags;

class Blogs extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    protected $fillable = [
        'banner',
        'banner_alt',
        'heading',
        'slug',
        'category_id',
        'read_time',
        'description',
        'short_description',
        'faq_schema',
        'status',
        'author_id',
        'created_by',
    ];

    protected $casts = [
        'status'      => 'integer',
        'category_id' => 'integer',
        'author_id'   => 'integer',
        'created_by'  => 'integer',
    ];

    /**
     * Create a blog post.
     * status: 0 = draft (DEFAULT), 1 = published.
     * A post is ONLY published when status=1 is explicitly passed.
     */
    public static function create(array $data)
    {
        $b = new Blogs;
        $b->heading           = $data['heading'];
        // FIX #2: banner was in $fillable but never assigned here - uploads were lost
        $b->banner            = !empty($data['banner']) ? $data['banner'] : '';
        $b->banner_alt        = !empty($data['banner_alt']) ? $data['banner_alt'] : '';
        $b->slug              = $data['slug'];
        $b->description       = $data['description'];
        // FIX: missing keys no longer throw undefined-index errors
        $b->read_time         = !empty($data['read_time']) ? $data['read_time'] : '5 min read';
        $b->short_description = $data['short_description'];
        $b->faq_schema        = !empty($data['faq_schema']) ? $data['faq_schema'] : '';
        $b->category_id       = !empty($data['category_id']) ? (int) $data['category_id'] : 32;
        // author_id must come from the request / authenticated context.
        // The rand(1,5) fallback that rotated authors lived in BlogApiController -
        // it must NOT be reintroduced here or there.
        $b->author_id         = (int) $data['author_id'];
        $b->status            = array_key_exists('status', $data) && $data['status'] !== null
                                    ? (int) $data['status']
                                    : 0; // DEFAULT DRAFT - never publish implicitly
        $b->created_by        = !empty($data['created_by']) ? (int) $data['created_by'] : 0;
        $b->save();

        // FIX #1: was `if(isset($tags))` - $tags did not exist yet, so this
        // branch NEVER ran and tags were silently discarded on every create.
        if (!empty($data['tags'])) {
            $tags = is_array($data['tags']) ? $data['tags'] : explode(',', (string) $data['tags']);
            foreach ($tags as $val) {
                $val = trim($val);
                if ($val === '') {
                    continue;
                }
                $t          = new BlogTags;
                $t->blog_id = $b->id;
                $t->tag     = $val;
                $t->save();
            }
        }

        return $b->id;
    }

    /**
     * Update a blog post. All fields optional - only provided keys change.
     * FIX #3: status can now be updated, so posts can be UNPUBLISHED
     * (status=0) or published (status=1) through this path.
     */
    public static function blog_update($id, array $data)
    {
        $b = Blogs::find($id);
        if (!$b) {
            return null;
        }

        $b->heading           = $data['heading']           ?? $b->heading;
        $b->slug              = $data['slug']              ?? $b->slug;
        $b->description       = $data['description']       ?? $b->description;
        $b->read_time         = $data['read_time']         ?? $b->read_time;
        $b->short_description = $data['short_description'] ?? $b->short_description;

        if (array_key_exists('banner', $data)) {
            $b->banner = $data['banner'];
        }
        if (array_key_exists('banner_alt', $data)) {
            $b->banner_alt = $data['banner_alt'];
        }
        if (array_key_exists('faq_schema', $data)) {
            $b->faq_schema = !empty($data['faq_schema']) ? $data['faq_schema'] : '';
        }
        if (!empty($data['category_id'])) {
            $b->category_id = (int) $data['category_id'];
        }
        if (!empty($data['author_id'])) {
            $b->author_id = (int) $data['author_id'];
        }
        // FIX #3: allow explicit status change (0 = draft/unpublish, 1 = publish)
        if (array_key_exists('status', $data) && $data['status'] !== null) {
            $b->status = (int) $data['status'];
        }

        $b->save();

        // FIX: only touch tags when the key is actually sent - a partial update
        // without 'tags' previously crashed on explode() AND wiped existing tags.
        if (array_key_exists('tags', $data)) {
            BlogTags::where('blog_id', $id)->delete();
            $tags = is_array($data['tags']) ? $data['tags'] : explode(',', (string) $data['tags']);
            foreach ($tags as $val) {
                $val = trim($val);
                if ($val === '') {
                    continue;
                }
                $t          = new BlogTags;
                $t->blog_id = $b->id;
                $t->tag     = $val;
                $t->save();
            }
        }

        return $b->id;
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(BlogTags::class, 'blog_id', 'id');
    }
}
