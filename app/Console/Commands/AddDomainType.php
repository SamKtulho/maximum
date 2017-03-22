<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;

class AddDomainType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:type';

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
        Domain::chunk(200, function ($domains) {
            foreach ($domains as $domain) {
                if ($domain->type == Domain::TYPE_EMAIL && $domain->links->first()) {
                    $domain->type = Domain::TYPE_LINK;
                    $domain->save();
                }
            }
        });

    }
}
