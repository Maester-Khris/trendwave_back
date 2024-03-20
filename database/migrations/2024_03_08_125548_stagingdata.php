<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;

return new class extends Migration
{

    public function up()
    {
        Schema::create("staging_post",function(Blueprint $table){
            $table->id();
            $table->integer('userId');
            $table->integer('postId');
            $table->string('title');
            $table->text('body');
        });

        foreach(self::fetchTestdata() as $pt){
            DB::table('staging_post')->insert(
                array(
                    'userId' => $pt['userId'],
                    'postId' => $pt['id'],
                    'title' => $pt['title'],
                    'body' => $pt['body'],
                )
            );
        }
    }

    public function down()
    {
    }

    public function fetchTestdata(){
        $response = Http::acceptJson()->get('https://jsonplaceholder.typicode.com/posts');
        return $response->json();
    }
};
