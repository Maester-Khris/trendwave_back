<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SseDataPusher extends Controller
{
    public function getDataStream(Request $request){
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($request) {
            while(true){
                $new_items = $this->getDBLastContent();
                ob_flush();
                sleep(1);
            }
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }

    public function getDBLastContent(){
        $filepath = resource_path('data/posts_db.json');
        $jsoncontent= file_get_contents($filepath);
        $data = json_decode($jsoncontent,true);
        $db = collect($data);
        $last = $db->last();

        $items = $db->filter(function($item) use ($last){
            if(isset($item['updated_at'])){
                return $item['updated_at'] >= $last['last_check'];
            }
        });
        return $items->toJson();
    }
}


// while(true) {
//     $notif = DB::table("staging_post") ->get();
//     echo 'data: ' . json_encode($notif) . "\n\n";
//     ob_flush();
//     usleep(200000);
// }
// $data = [];
// for($i=0; $i>100; $i++){
//     $data[]=$i;
// }
// foreach($data as $item){ 
//     echo 'data: ' .json_encode($item) . "\n\n";; 
//     ob_flush(); 
//     flush(); 
//     sleep(1); 
// }