<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Models\Coupon;
use Carbon\Carbon;

class GetCoupons extends Command
{

    const SOURCE_CITYADS = 1;

    private $cityadsUrl = 'http://ru-geo.cityads.com/api/rest/webmaster/json/coupons?remote_auth=208ac3d579f498bca6cc4271118c2868&filter=NDQ5NzczNjI1&limit=1000';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:coupons';

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
        $this->info('Getting coupons');
        $this->info('Cityads');

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->cityadsUrl);
        $data = (\GuzzleHttp\json_decode((string) $response->getBody(), true));

        if (isset($data['status']) && $data['status'] === 200 && ($items = $data['data'])) {
            $coupons = $items['items'];
            foreach ($coupons as $coupon) {
                if (Coupon::where(['id' => $coupon['id'], 'source' => self::SOURCE_CITYADS])->first()) {
                    continue;
                }
                $this->info($coupon['id']);

                $coupon['id'] = (int)$coupon['id'];
                $coupon['source'] = self::SOURCE_CITYADS;
                $coupon['start_date'] = Carbon::parse($coupon['start_date'])->toDateTimeString();
                $coupon['active_to'] = Carbon::parse($coupon['active_to'])->toDateTimeString();

                $couponModel = new Coupon();
                $couponModel->fill($coupon);
                $couponModel->save();
            }
        }
            $this->info('Done');
        }
}
