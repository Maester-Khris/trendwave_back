<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PostController extends Controller
{    
    private $post_json;

    public function __construct() {
        $filepath = resource_path('data/posts_db.json');
        $jsoncontent= file_get_contents($filepath);
        $data = json_decode($jsoncontent,true);
        $this->post_json = $data;
    }

    // ========== Brows & Read Methods ===================
    public function getAllPost(){
        $data = $this->post_json;
        return response()->json($data, 200);
    }
    public function getPostById(Request $request, $postid){
        $data = $this->post_json;
        $post = collect($data)->where('id',$postid)->first();
        if($post){
            return response()->json($post, 200);
        }else{
            return response()->json(["message"=>"Resource not found"], 404);
        }   
    }

    // ========== Edit Methods ===================
    public function newLike($postid){
        $data = $this->post_json;
        $post = collect($data)->where('id',$postid)->first();
        if($post){
            $updated = collect($data)->map(function($item) use ($postid){
                if(isset($item['id']) && $item['id']==$postid){ 
                    $item['like'] = $item['like'] + 1 ;
                    $item['updated_at'] = Carbon::now();
                }
                return $item;
            });
            $this->saveUpdate($updated);
            return response()->json(["message"=>"post like updated"], 200);
        }else{
            return response()->json(["message"=>"Resource not found"], 404);
        }
    }
    public function newRetweet($postid){
        $data = $this->post_json;
        $post = collect($data)->where('id',$postid)->first();
        if($post){
            $updated = collect($data)->map(function($item) use ($postid){
                if(isset($item['id']) && $item['id']==$postid){ 
                 $item['retweet'] = $item['retweet'] + 1 ;
                }
                return $item;
            });
            $this->saveUpdate($updated);
            return response()->json(["message"=>"post tweets updated"], 200);
        }else{
            return response()->json(["message"=>"Resource not found"], 404);
        }
    }
    public function addNewHashtag(Request $request, $postid){
        $data = $this->post_json;
        $post = collect($data)->where('id',$postid)->first();
        if($post){
            if(count($request->hastags)>0){
                $updated = collect($data)->map(function($item) use ($postid, $request){
                    if(isset($item['id']) && $item['id']==$postid){ 
                        $item['hashtag'] =  array_merge($item['hashtag'], $request->hastags) ;
                    }
                    return $item;
                });
                $this->saveUpdate($updated);
                return response()->json(["message"=>"post hastags updated"], 200);
            }else{  
                return response()->json(["message"=>"Bad data input"], 404);
            }
        }else{
            return response()->json(["message"=>"Resource not found"], 404);
        }
    }

    // ========== Add Methods ===================
    public function addNewPost(Request $request){
        $data = $this->post_json;
        if(isset($request->post) && !empty($request->post) && $this->checkAllKeys($request->post)){
            $post = $request->post;
            $post['id'] = count($data) + 1;
            $post['published_time'] = Carbon::now();
            $updated = collect($data)->push($post);
            $this->saveUpdate($updated);
            return response()->json(["message"=>"post added"], 200);
        }else{
            return response()->json(["message"=>"Bad data input"], 400);
        }
    }

    // ========== Helper Methods ===================
    public function saveUpdate($new_post_json){
        file_put_contents(
            resource_path('data/posts_db.json'),
            $new_post_json->toJson()
        );
    }
    public function checkAllKeys($post_json_input){
        $keys = ['tags','hashtag','like','username','platform','retweet','country','post'];
        $diff_len = count(array_diff(array_keys($post_json_input), $keys));
        return $diff_len == 0 ? true : false;
    }
}
