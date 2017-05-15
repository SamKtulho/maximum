<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Shorturl;

class ManualController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_user');

    }
    
    public function count()
    {
        return response()->json(['response' => \App\Services\Manual::getLinksCount()]);
    }
    
    public function subdomainCount()
    {
        return response()->json(['response' => \App\Services\Manual::getSubdomainsCount()]);
    }

    public function notFound(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $domainId = $request->get('id', null);
        $domain = Domain::find($domainId);
        $domain->status = Domain::STATUS_NOT_PROCESSED;
        $domain->save();
        $modelLink = Link::where('domain_id', $domainId)->first();
        $modelLink->status = Link::STATUS_NOT_PROCESSED;
        $modelLink->save();
        Shorturl::where('domain_id', $domainId)->delete();
        
        return response()->json(['response' => 'Домен отправлен в генератор через регистраторов.']);
    }
}
