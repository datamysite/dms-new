<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blogs;
use App\Models\BlogTags;
use App\Models\TagData;
use App\Models\Categories;
use URL;

class BlogController extends Controller
{
    
    public function index(){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        $data['title'] = 'Blogs';
        if(!empty($_GET['page'])){
            $data['tags'] = '1';
        }
        $data['data'] = Blogs::where('status', '1')->orderBy('created_at', 'desc')->paginate(25);

        return view('web.blogs.index')->with($data);
    }

    // Blogs landing V2 preview (same data as index(), redesigned template)
    public function indexNew(){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        $data['title'] = 'Blogs';
        if(!empty($_GET['page'])){
            $data['tags'] = '1';
        }
        $data['data'] = Blogs::where('status', '1')->orderBy('created_at', 'desc')->paginate(25);

        return view('web.blogs-v2')->with($data);
    }

    // Category listing V2 preview (same data as category())
    public function categoryNew($slug){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        if(!empty($_GET['page'])){
            $data['tags'] = '1';
        }
        $category = Categories::where('slug', $slug)->first();
        if(!empty($category->id)){
            $data['title'] = $category->name;
            $data['type'] = 'category';
            $data['page'] = '- Blogs';
            $data['data'] = Blogs::where('status', '1')->where('category_id', $category->id)->orderBy('created_at', 'desc')->paginate(25);

            return view('web.blogs-v2')->with($data);
        }
        return redirect(route('blogs'));
    }

    // Tag listing V2 preview (same data as tags())
    public function tagsNew($slug){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        $rdata = TagData::where('slug', $slug)->first();
        $data['tags'] = '1';
        if(!empty($rdata->id)){
            $tag = BlogTags::where('tag', $rdata->tag)->first();
            if(!empty($tag->id)){
                $data['title'] = ucwords($tag->tag).' - Tag';
                $data['type'] = 'category';
                $data['page'] = '';
                $data['data'] = Blogs::where('status', '1')
                                        ->whereHas('tags', function($q) use ($tag){
                                            return $q->where('tag', $tag->tag)->orWhere('tag', ' '.$tag->tag);
                                        })
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(25);
                $data['ametaTags'] = [
                    'title' => ucwords($tag->tag).' - DataMySite Tag Archives',
                    'keywords' => $tag->tag,
                    'description' => $rdata->description
                ];

                return view('web.blogs-v2')->with($data);
            }
        }
        return redirect(route('blogs'));
    }

    // All categories V2 preview (same data as allCategories())
    public function allCategoriesNew(){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        $data['title'] = 'All Blog Categories';
        if(!empty($_GET['page'])){
            $data['tags'] = '1';
        }
        $data['categories'] = Categories::withCount('blogs')->where('status', '1')->orderByDesc('blogs_count')->get();

        return view('web.blogs-categories-v2')->with($data);
    }

    // Blog post V2 preview (same data as details(), redesigned template)
    public function detailsNew($blog_slug){
        $data['nav'] = 'blogs';
        $data['data'] = Blogs::where('slug', $blog_slug)->where('status', '1')->firstOrFail();
        $data['related'] = Blogs::where('slug', '!=', $blog_slug)->where('status', '1')->orderBy('created_at', 'desc')->limit(3)->get();
        $data['og_img'] = URL::to('public/storage/blogs/'.$data['data']->banner);

        return view('web.blog-detail-v2')->with($data);
    }

    public function category($slug){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        if(!empty($_GET['page'])){
            $data['tags'] = '1';
        }
        $category = Categories::where('slug', $slug)->first();
        if(!empty($category->id)){
            $data['title'] = $category->name;
            $data['type'] = 'category';
            $data['page'] = '- Blogs';
            $data['data'] = Blogs::where('status', '1')
                                    ->where('category_id', $category->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(25);

            return view('web.blogs.index')->with($data);
        }else{
            return redirect(route('blogs'));
        }
    }

    public function details($blog_slug){
        $data['nav'] = 'blogs';
        
        $data['data'] = Blogs::where('slug', $blog_slug)->where('status', '1')->firstOrFail();
        

        $data['related'] = Blogs::where('slug', '!=', $blog_slug)->where('status', '1')->orderBy('created_at', 'desc')->limit(3)->get();
        $data['og_img'] = URL::to('public/storage/blogs/'.$data['data']->banner);
        
        return view('web.blogs.details')->with($data);
    }

    public function tags($slug){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        $rdata = TagData::where('slug', $slug)->first();
        $data['tags'] = '1';

        if(!empty($rdata->id)){

            $tag = BlogTags::where('tag', $rdata->tag)->first();
            if(!empty($tag->id)){
                $data['title'] = ucwords($tag->tag).' - Tag';
                $data['type'] = 'category';
                $data['page'] = '';
                $data['data'] = Blogs::where('status', '1')
                                        ->whereHas('tags', function($q) use ($tag){
                                            return $q->where('tag', $tag->tag)
                                                        ->orWhere('tag', ' '.$tag->tag);
                                        })
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(25);

                $data['ametaTags'] = [
                    'title' => ucwords($tag->tag).' - DataMySite Tag Archives',
                    'keywords' => $tag->tag,
                    'description' => $rdata->description
                ];
                //dd($data['metaTags']['title']);
                return view('web.blogs.index')->with($data);
            }else{
                return redirect(route('blogs'));
            }
        }else{
            return redirect(route('blogs'));
        }
    }
    
    public function allCategories(){
        $data['nav'] = 'blogs';
        $data['titleImg'] = 'services.jpg';
        $data['title'] = 'All Blog Categories';
        if(!empty($_GET['page'])){
            $data['tags'] = '1';
        }
        $data['categories'] = Categories::withCount('blogs')->where('status', '1')->orderByDesc('blogs_count')->get();

        return view('web.blogs.categories')->with($data);
    }
}
