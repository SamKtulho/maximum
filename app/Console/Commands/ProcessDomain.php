<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use App\Models\Email;
use App\Models\Link;
use GuzzleHttp\Client;

class ProcessDomain extends Command
{
    private $stopWords = [
        'abuse',
        'domain@fb.com',
        'domains@tinyurl.com',
        'nic.ru',
        'help@whois.in',
        'info@reg.ru',
        'regprivate',
        'service',
        'hostmaster',
        'WHOIS',
        'gutako@list.ru',
        '.whoi',
        '@tldregistrarsolutions.com',
        '@1and1.com',
        '@telderi.ru',
        '@masterhost.ru',
        '@nethouse.ru',
        '@ittown.org',
        '@domaindiscreet.com',
        '@netlevel.ru',
        '@majordomo.ru',
        '@hostpro.com',
        '@domainidshield.com',
        '@contactprivacy.com',
        '@hoster.ru',
        '@whoisguard.com',
        '@nurhost.kz',
        '@safewhois.ca',
        '@monikerprivacy.ne',
        '@r01.ru',
        '@naunet.ru',
        '@hoster.by',
        '@privacy.com',
        '@support.hostpro.ua',
        '@domaincontext.com',
        '@musico.cc',
        '@privacyguardian.org',
        '@whoisprivacyprotect.dp',
        'Rating@Mail.ru',
        'info@uahosting.com',
        'contact@privacyprotect.org',
        'registry@vegatele.com',
        '@contact.gand',
        '@private.desi',
        '@fablovkawhoisprotection.com',
    ];

    private $subdomainToEmail = [
        '\.com\.ru',
        '\..+\.ua',
        '\.nov\.ru',
        '\.nnov\.ru',
        '\.spb\.ru',
    ];

    private $subdomainToLink = [
        '\.net\.ru',
        '\.org\.ru',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:process';

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
        Domain::where('type', Domain::TYPE_UNKNOWN)->chunk(200, function ($domains) {
            $subdomainToEmailPattern = '~';
            foreach ($this->subdomainToEmail as $id => $pattern) {
                $subdomainToEmailPattern .= $id ? '|(' . $pattern . ')' : '(' . $pattern . ')';
            }
            $subdomainToEmailPattern .= '~';

            $subdomainToLinkPattern = '~';
            foreach ($this->subdomainToLink as $id => $pattern) {
                $subdomainToLinkPattern .= $id ? '|(' . $pattern . ')' : '(' . $pattern . ')';
            }
            $subdomainToLinkPattern .= '~';

            foreach ($domains as $domainModel) {
                $this->info($domainModel->domain);
                $isSubdomainExclude = false;
                if ((substr_count($domainModel->domain, '.') === 1
                        || preg_match($subdomainToLinkPattern, $domainModel->domain))
                    && preg_match('~(\.ru)$|(\.рф)$~', $domainModel->domain)
                    && !($isSubdomainExclude = preg_match($subdomainToEmailPattern, $domainModel->domain))
                ) {
                    $link = new Link();
                    $link->link = 'https://www.reg.ru/whois/admin_contact?dname=' . urlencode(iconv('utf-8', 'windows-1251', $domainModel->domain));
                    $link->domain_id = $domainModel->id;
                    $link->status = Link::STATUS_NOT_PROCESSED;
                    $link->registrar = null;
                    try {
                        $link->save();
                        $domainModel->type = Domain::TYPE_LINK;
                        $domainModel->save();
                        $this->info('Link');

                    } catch (\Exception $e) {
                        continue;
                    }
                } elseif ($isSubdomainExclude || substr_count($domainModel->domain, '.') === 1) {
                    $domainModel->type = Domain::TYPE_EMAIL;

                    $result = [];
                    $client = new Client();
                    try {
                        $request = $client->request('GET', 'https://www.reg.ru/whois/?dname=' . $domainModel->domain,
                            ['allow_redirects' => [
                                'max'             => 10,
                                'strict'          => true,
                                'referer'         => true,
                                'track_redirects' => true
                            ],
                                'connect_timeout' => 15
                            ]
                        );
                    } catch (\GuzzleHttp\Exception\ConnectException $e) {
                        $domainModel->status = Domain::STATUS_DELAYED;
                        $domainModel->save();
                        continue;
                    } catch (\Exception $e) {
                        continue;
                    }

                    if ($responseBody = $request->getBody()) {
                        preg_match_all('~[-\w.]+@[A-z0-9][-A-z0-9]+\.+[A-z]{2,4}~', $responseBody, $matches);
                        $result = [];
                        if (!empty($matches[0]) && is_array($matches[0])) {
                            $result = array_unique(array_diff($matches[0], []));
                        }

                        foreach ($result as $key => $item) {
                            foreach ($this->stopWords as $stop) {
                                if (strpos($item, $stop) !== false) {
                                    unset($result[$key]);
                                }
                            }

                        }
                    }
                    $email = strtolower(reset($result));
                    if ($email && !Email::where('email', $email)->first()) {
                        $emailModel = new Email();
                        $emailModel->email = $email;
                        $emailModel->domain_id = $domainModel->id;
                        $emailModel->is_valid = Email::STATUS_VALID;
                        try {
                            $emailModel->save();
                            $this->info('Email ' . $email);

                        } catch (\Exception $e) {
                            continue;
                        }
                    } elseif (!$email) {
                        $domainModel->status = Domain::STATUS_EMAIL_NOT_FOUND;
                    }
                    $domainModel->save();

                } else {
                    try {
                        $domainModel->type = Domain::TYPE_SUBDOMAIN;
                        $domainModel->save();
                        $this->info('Subdomain');
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        });
    }
}
