<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shorturl;
use App\Models\Urlstat;
use App\Services\GoogleUrlApi;
use Carbon\Carbon;

class GetStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get statistic';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $statFieldName = 'analytics';
    private $shortClickField = 'shortUrlClicks';
    private $longClickField = 'longUrlClicks';

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
        Shorturl::where('type', Shorturl::TYPE_GOOGLE)->orwhere('type', Shorturl::TYPE_REGISTRAR)->orderby('id', 'desc')->chunk(200, function ($urls) {
            foreach ($urls as $url) {

                if (Carbon::parse($url->created_at)->diffInDays(Carbon::now()) > 7) {
                    continue;
                }

                $googleApi = new GoogleUrlApi(GoogleUrlApi::KEY,
                    'shortUrl='. urlencode($url->url) . '&projection=ANALYTICS_CLICKS&fields=analytics'
                );

                $stat = ($googleApi->send(null, false));

                if ($stat && !empty($stat[$this->statFieldName])) {
                    $urlStat = Urlstat::where('shorturl_id', $url->id)->first();
                    if (!$urlStat) {
                        $urlStat = new Urlstat();
                        $urlStat->shorturl_id = $url->id;
                    }
                    $urlStat->stat = serialize($stat[$this->statFieldName]);
                    $urlStat->save();
                }
            }
            sleep(10);
        });
    }
}
