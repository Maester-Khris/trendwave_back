<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\PostV1;

class UtilMethod extends Controller
{
    const WORDCLOUD_API = "https://quickchart.io/wordcloud";

    public function noMatter(){
        // $filename = 'wordclouds/' . $this->getDateString() . '.jpg';
        // $wordcloud_url = Storage::url($filename);
        // // dd($wordcloud_url);
        // session()->put('DAILY_WORDCLOUD_PUBLIC_URL',$wordcloud_url);
        // ==============================================
        // $filepath = resource_path('data/posts_db.json');
        // $jsoncontent= file_get_contents($filepath);
        // $data = json_decode($jsoncontent,true);
        // $db = collect($data);
        // $last = $db->pop();
        // $last['last_check'] = Carbon::now();
        // $db->push($last);
        // file_put_contents(resource_path('data/posts_db.json'), $db->toJson());

        // ============================================
        
        // $filepath = resource_path('data/posts_db.json');
        // $jsoncontent= file_get_contents($filepath);
        // $data = json_decode($jsoncontent,true);
        // $db = collect($data);
        // $last = $db->last();

        // $items = $db->filter(function($item) use ($last){
        //     if(isset($item['updated_at'])){
        //         return $item['updated_at'] >= $last['last_check'];
        //     }
        // });
        // dd( $items->toJson());
    }

    public function parseCsvtoJSON(){
        $filepath = public_path('data/sentimentdataset.csv');
        $csvFileContent= file_get_contents($filepath);
        $csvLineArray = explode("\n", $csvFileContent);
        $result = array_map("str_getcsv", $csvLineArray);
        array_shift($result);
        
        $final = collect();
        foreach($result as $key => $res){
            $res_collection = collect($res);
            $post = new PostV1;
            $post->id =  $key+1;
            $post->username = trim($res_collection->values()->get(5));
            $post->post = trim($res_collection->values()->get(2));
            $post->platform = trim($res_collection->values()->get(6));
            $post->retweets = trim($res_collection->values()->get(8));
            $post->likes = trim($res_collection->values()->get(9));
            $post->country = trim($res_collection->values()->get(10));
            $post->hashtag = self::removeEmptyStringfromArray(explode(" ",$res_collection->values()->get(7)));
            $post->published_datetime = Carbon::parse( $res_collection->values()->get(4));
            $post->created_at = Carbon::now();
            $post->updated_at = Carbon::now();
            $final->push($post);
        } 
        $final[]= ['created_at'=>Carbon::now(), 'last_check'=>Carbon::now()];
        $jsonObject = $final->toJson();
        $savepath = resource_path('data/posts_db.json');
        file_put_contents($savepath, $jsonObject);
    }

    /**
     * 1- generate the wordcloud
     * 2- store the wordcloud in storage/public/wordclouds with the date as filename
     * 3- genereate a public url for the wordcloud and store it in session
    */
    public function generateWordCloud(){
        $client = new Client();
        $response = $client->post(self::WORDCLOUD_API, [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'format'=> 'png',
                'width'=> 1000,
                'height'=> 1000,
                'fontScale'=> 15,
                'scale'=> 'linear',
                'removeStopwords'=> true,
                'minWordLength'=> 4,
                'text'=> "Lorem ipsum dolor sit amet consectetur, adipisicing elit.  Vero fuga omnis, officiis, eum tempore labore quia dicta molestiae tenetur voluptatem, harum in quo magnam quisquam et excepturi. Eius, impedit cupiditate id at facere optio excepturi corrupti atque dicta. Quisquam nisi unde voluptate dolor rem quo distinctio incidunt iusto deleniti asperiores doloremque quis repellat consectetur quaerat, fugit sint ea, ratione inventore."
            ],
        ]);
        $filename = 'wordclouds/' . $this->getDateString() . '.jpg';
        $stream = \GuzzleHttp\Psr7\Utils::streamFor($response->getBody()); 
        Storage::disk('local')->put($filename, $stream->getContents()); 
        $wordcloud_url = Storage::url($filename);
        session()->put('DAILY_WORDCLOUD_PUBLIC_URL',$wordcloud_url);
        // move all this in a cron task scheduled every day at 00h30.
    }
    public function homeViewData(){
       
    }

    // =========== Helper =============
    public function removeEmptyStringfromArray($data){
        $clean = [];
        foreach($data as $dt){
            if(!empty($dt)){
                $clean[] = $dt;
            }
        }
        return $clean;
    }
    public function getDateString(){
        $today = Carbon::now();
        return $today->year . '_' . $today->month . '_'  . $today->day;
    }
}

