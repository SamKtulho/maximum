<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use GuzzleHttp\Client;

class SaveRussian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:russian';

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
        $maxId = file_get_contents('maxid');

        Domain::where('status', Domain::STATUS_MODERATE)->where('id', '>', $maxId)->chunk(200, function ($domains) {
            foreach ($domains as $domainModel) {
                file_put_contents('maxid', $domainModel->id);

                $this->info($domainModel->domain);

                if (strpos($domainModel->domain, '.ru') !== false) {
                    continue;
                }

                $client = new Client();
                try {
                    $response = $client->request('GET', $domainModel->domain,
                        ['allow_redirects' => [
                            'max'             => 4,
                            'strict'          => true,
                            'referer'         => true,
                            'track_redirects' => true
                        ],
                            'connect_timeout' => 10
                        ]
                    );
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    continue;
                }

                $header = $response->getHeader('content-type');
//dd($header);
                if ($responseBody = $response->getBody()) {
                    if (strtolower(reset($header)) === 'text/html'
                        || strpos(strtolower(reset($header)), '1251') !== false
                        || preg_match('~[А-Яа-я]+~u', (string) $responseBody))
                    {
                        $this->info('OK');
                    } else {
                        $this->error('Drop!');
                        $domainModel->status = Domain::STATUS_BAD;
                        $domainModel->save();
                    }
                }
            }

        });
    }
}
