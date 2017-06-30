<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Shorturl;
use Yajra\Datatables\Datatables;

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

    public function updateAction(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'action' => 'required'
        ]);

        $domainId = $request->get('id', null);
        $action = $request->get('action', 0);

        if ($action == Shorturl::ACTION_NOT_FOUND || $action == Shorturl::ACTION_BAD_DOMAIN) {
            $domain = Domain::find($domainId);
            $domain->status = $action == Shorturl::ACTION_BAD_DOMAIN ? Domain::STATUS_BAD : Domain::STATUS_NOT_PROCESSED;
            $domain->save();
            $modelLink = Link::where('domain_id', $domainId)->first();
            $modelLink->status = Link::STATUS_NOT_PROCESSED;
            $modelLink->save();
            //Shorturl::where('domain_id', $domainId)->delete();
            $shorturl = Shorturl::where('domain_id', $domainId)->first();
            $shorturl->action = $action;
            $shorturl->save();
            
            $message = $action == Shorturl::ACTION_BAD_DOMAIN ? 'Домен отбракован' : 'Домен отправлен в генератор через регистраторов.';
            return response()->json(['response' => $message]);
        } else {
            $shorturl = Shorturl::where('domain_id', $domainId)->first();
            $shorturl->action = $action;
            $shorturl->save();

        }
        return response()->json(['response' => 'Домен обработан. Спасибо.']);

    }

    public function foundLog()
    {
        return view('manual.found_log', ['logs' => []]);
    }

    public function foundLogData(Datatables $datatables)
    {
        $query = Shorturl::with('user')->with('domain')->where('shorturls.type', Shorturl::TYPE_REGISTRAR);

        return $datatables->eloquent($query)
            ->make(true);
    }
}
