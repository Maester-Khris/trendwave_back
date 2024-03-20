<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Carbon\Carbon;

class DailyWordCloud extends Command
{

    protected $signature = 'wordcloud:generate';
    protected $description = 'use this command to generate and store a wordcloud daily';
    const WORDCLOUD_API = "https://quickchart.io/wordcloud";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
        $filename = 'public/wordclouds/' . $this->getDateString() . '.jpg';
        $stream = \GuzzleHttp\Psr7\Utils::streamFor($response->getBody()); 
        Storage::disk('local')->put($filename, $stream->getContents()); 
        $wordcloud_url = Storage::url($filename);
        session()->put('DAILY_WORDCLOUD_PUBLIC_URL',$wordcloud_url);
        // echo "SUCCESS";
        return Command::SUCCESS;
    }

    public function getDateString(){
        $today = Carbon::now();
        return $today->year . '_' . $today->month . '_'  . $today->day . '_'  . $today->hour . '_'  . $today->minute;
    }
}
