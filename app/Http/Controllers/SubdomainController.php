<?php

namespace App\Http\Controllers;

use App\Models\Shorturl;
use Yajra\Datatables\Datatables;

class SubdomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_user');

    }
    
    public function data(Datatables $datatables)
    {
        $query = Shorturl::with('user')->with('domain')->with('urlstats')->select('shorturls.*')->where('shorturls.type', Shorturl::TYPE_SUBDOMAIN);

        return $datatables->eloquent($query)
            ->addColumn('stat', function ($shorturl) {
                return isset($shorturl->urlstats[0]) ? unserialize($shorturl->urlstats[0]->stat)['allTime']['shortUrlClicks'] : '?';
            })
            ->make(true);
    }

    public function statistic()
    {
        return view('subdomain.statistic', ['shortUrls' => []]);
    }
}
