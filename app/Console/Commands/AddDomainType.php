<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;

class AddDomainType extends Command
{

    private $subdomainToExclude = [
        '\.com\.ru',
        '\.nov\.ru',
        '\.net\.ru',
        '\.org\.ru',
    ];

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
        $subdomainToExcludePattern = '~';
        foreach ($this->subdomainToExclude as $id => $pattern) {
            $subdomainToExcludePattern .= $id ? '|(' . $pattern . ')' : '(' . $pattern . ')';
        }
        $subdomainToExcludePattern .= '~';


        Domain::chunk(200, function ($domains) use ($subdomainToExcludePattern) {
            foreach ($domains as $domain) {
                if ($domain->type != Domain::TYPE_SUBDOMAIN
                    && substr_count($domain->domain, '.') > 1
                    &&  !preg_match($subdomainToExcludePattern, $domain->domain)
                ) {
                    $domain->type = Domain::TYPE_SUBDOMAIN;
                    $domain->save();
                    $this->info($domain->domain);
                }
            }
        });

    }
}
