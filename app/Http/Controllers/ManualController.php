<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Shorturl;
use Illuminate\Support\Facades\DB;
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

    public function emailCount()
    {
        return response()->json(['response' => \App\Services\Manual::getEmailsCount()]);
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
            if ($modelLink = Link::where('domain_id', $domainId)->first()) {
                $modelLink->status = Link::STATUS_NOT_PROCESSED;
                $modelLink->save();
            }

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
        $query = Shorturl::with('user')->with('domain')->where(function ($query) {
            $query->orWhere('shorturls.type', Shorturl::TYPE_REGISTRAR)->
            orWhere('shorturls.type', Shorturl::TYPE_GOOGLE);
        });

        return $datatables->eloquent($query)
            ->make(true);
    }

    public function report()
    {
        $daysBefore = 7;
        $range = [];
        for ($i = 0; $i < $daysBefore; ++$i) {
            $range[] = strtotime(date('Y-m-d', strtotime('-' . $i . ' days')));
        }

        $statDBUser = DB::table('shorturls')
            ->join('users', 'users.id', '=', 'shorturls.user_id')
            ->where(function ($query) {
                $query->orWhere('type', Shorturl::TYPE_REGISTRAR)->
                orWhere('type', Shorturl::TYPE_GOOGLE);
            })
            ->where('action', '>', 0)
            ->select('user_id', 'action', 'users.name', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->groupBy('action')
            ->get();

        $statDBDate = DB::table('shorturls')
            ->where('created_at', '>', date('Y-m-d', strtotime('-'. $daysBefore . ' days')))
            ->where(function ($query) {
                $query->orWhere('type', Shorturl::TYPE_REGISTRAR)->
                orWhere('type', Shorturl::TYPE_GOOGLE);
            })
            ->where('action', '>', 0)
            ->select('action', 'created_at', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->groupBy('created_at')
            ->get()->toArray();

        $resultByDate = [];

        foreach ($statDBDate as $stat) {
            $statDate = strtotime(str_limit($stat->created_at, 10, ''));
            $resultByDate[$stat->action][$statDate] =
                isset($resultByDate[$stat->action][$statDate])
                    ? $resultByDate[$stat->action][$statDate] + $stat->total
                    : $stat->total;
        }


        foreach ($resultByDate as &$result) {
            foreach ($range as $date) {
                if (!isset($result[$date])) {
                    $result[$date] = 0;
                }
            }
            krsort($result);
        }

        unset($result);

        return view('manual.report', ['reportByUser' => $statDBUser, 'reportByDate' => $resultByDate]);
    }
}
