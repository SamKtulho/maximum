<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Link;
use GuzzleHttp\Client;


class ParseLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'links:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Link::where('registrar', null)->chunk(200, function ($urls) {
            foreach ($urls as $url) {

                $client = new Client();
                $res = $client->request('GET', $url->link,
                    ['allow_redirects' => [
                        'max'             => 10,
                        'strict'          => true,
                        'referer'         => true,
                        'track_redirects' => true
                    ]]
                );

                $redirects = $res->getHeaderLine('X-Guzzle-Redirect-History');

                if (empty($redirects)) {
                    $url->registrar = 'reg.ru';
                    $this->info($url->link . ' reg.ru');
                } elseif (strpos($redirects, 'nic.ru/') !== false) {
                    $url->registrar = 'nic.ru';
                    $this->info($url->link . ' nic.ru');
                } else {
                    $result = [];
                    preg_match('`(http[s]?://)(www.)?([A-Za-z\.0-9-а-я\_]+)/.*`', $redirects, $result);
                    $url->registrar = $result[3];
                    $this->info($url->link . ' ' .$result[3]);
                }
                $url->save();
            }
        });
    }
}