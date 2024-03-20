<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostV1 extends Model
{
    // $casts = ['options' => AsArrayObject::class];

    public function __construct($id=0,$username="", $post="", $platform="", $tags=[], $hashtag=[], $like=0, $retweet=0, $country="", $published_time="",$created_at="",$updated_at="") {
        $this->id = $id;
        $this->username = $username;
        $this->post = $post;
        $this->platform = $platform;
        $this->tags = $tags;
        $this->hashtag = $hashtag;
        $this->like = $like;
        $this->retweet = $retweet;
        $this->country = $country;
        $this->published_time = $published_time;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
