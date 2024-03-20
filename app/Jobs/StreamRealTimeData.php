<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class StreamRealTimeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(){}

    public function handle()
    {
        $new_items = $this->getDBLastContent();
        $this->updateDBLastCheck();
        
        $callback = function () use ($new_items) {
            echo "data: " . json_encode($new_items) . "\n\n";
            ob_flush();
            flush();
            sleep(4);
        };
        $response = response()->stream($callback, 200, [
            'Content-Type' => 'text/event-stream',
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'no-cache',
        ]);
        $response->send();
        
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

    public function updateDBLastCheck(){
        $filepath = resource_path('data/posts_db.json');
        $jsoncontent= file_get_contents($filepath);
        $data = json_decode($jsoncontent,true);
        $db = collect($data);
        $last = $db->pop();
        $last['last_check'] = Carbon::now();
        $db->push($last);
        file_put_contents(resource_path('data/posts_db.json'), $db->toJson());
    }
}
