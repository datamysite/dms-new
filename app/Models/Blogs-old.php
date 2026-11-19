<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\Countries;
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

    public static function create(array $data){
        $b = new Blogs;
        $b->heading = $data['heading'];
        $b->banner_alt = !empty($data['banner_alt']) ? $data['banner_alt'] : '';
        $b->slug = $data['slug'];
        $b->description = $data['description'];
        $b->read_time = $data['read_time'];
        $b->short_description = $data['short_description'];
        $b->faq_schema = !empty($data['faq_schema']) ? $data['faq_schema'] : '';
        $b->category_id = !empty($data['category_id']) ? $data['category_id'] : '32';
        $b->author_id = $data['author_id'];
        $b->status = array_key_exists('status', $data) && $data['status'] !== null ? (int) $data['status'] : 0;
        $b->created_by = $data['created_by'];
        $b->save();
        
        if(isset($tags)){
            $tags = explode(',',$data['tags']);
            foreach ($tags as $key => $val) {
                $t = new BlogTags;
                $t->blog_id = $b->id;
                $t->tag = $val;
                $t->save();
            }
        }

        return $b->id;
    }


    public static function blog_update($id, array $data){
        $b = Blogs::find($id);
        $b->heading = $data['heading'];
        $b->banner_alt = $data['banner_alt'];
        $b->slug = $data['slug'];
        $b->description = $data['description'];
        $b->read_time = $data['read_time'];
        $b->short_description = $data['short_description'];
        $b->faq_schema = !empty($data['faq_schema']) ? $data['faq_schema'] : '';
        $b->category_id = $data['category_id'];
        $b->author_id = $data['author_id'];
        $b->save();

        BlogTags::where('blog_id', $id)->delete();
        $tags = explode(',',$data['tags']);
        foreach ($tags as $key => $val) {
            $t = new BlogTags;
            $t->blog_id = $b->id;
            $t->tag = $val;
            $t->save();
        }

        return $b->id;
    }

    public function category(){
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }

    public function author(){
        return $this->belongsTo(Author::class, 'author_id', 'id');
    }

    public function tags(){
        return $this->hasMany(BlogTags::class, 'blog_id', 'id');
    }
}
