<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Domain;
use App\Models\Shorturl;
use App\Models\ModerationLog;
use Yajra\Datatables\Datatables;

class LinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_user');
    }

    public function create(Request $request)
    {
        return view('link.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
            'source' => 'required',
        ]);

        $content = $request->get('content', null);
        $source = $request->get('source', null);

        $content = explode(PHP_EOL, $content);
        $errorsCount = $successCount = 0;

        foreach ($content as $string) {
            $string = trim($string);
            $data = explode('=', $string);

            if (!isset($data[1])) {
                ++$errorsCount;
                continue;
            }

            $domain = new Domain;
            $domain->domain = $data[1];
            $domain->tic = 10;
            $domain->type = Domain::TYPE_LINK;
            $domain->source = $source;
            $domain->status = Domain::STATUS_MODERATE;

            try {
                $domain->save();
            } catch (\Exception $e) {
                ++$errorsCount;
                continue;
            }

            $linkModel = new Link();
            $linkModel->link = $string;
            $linkModel->domain_id = $domain->id;
            $linkModel->status = Link::STATUS_NOT_PROCESSED;
            $linkModel->save();
            ++$successCount;
        }
        $request->session()->flash('alert-success', 'Готово. Успешно ' . $successCount . ' линков. С ошибкой ' . $errorsCount . ' линков.');
        return redirect('/link/create');
    }
    
    public function data(Datatables $datatables)
    {
        $query = Shorturl::with('user')->with('domain')->with('urlstats')->select('shorturls.*')->where('type', Shorturl::TYPE_REGISTRAR)->whereIn('action', [Shorturl::ACTION_FORM_SENT, Shorturl::ACTION_MAIL_SENT]);

        return $datatables->eloquent($query)
            ->addColumn('stat', function ($shorturl) {
                return isset($shorturl->urlstats[0]) ? unserialize($shorturl->urlstats[0]->stat)['allTime']['shortUrlClicks'] : '?';
            })
            ->make(true);
    }

    public function statistic()
    {
        return view('link.statistic', ['shortUrls' => []]);
    }

    public function count()
    {
        return response()->json(['response' => \App\Services\Link::getLinksCount()]);
    }

    public function moderationLog()
    {
        return view('link.moderation_log', ['logs' => []]);
    }

    public function moderationLogData(Datatables $datatables)
    {
        $query = ModerationLog::with('user')->with('domain')->where('moderation_logs.type', ModerationLog::TYPE_LINK);

        return $datatables->eloquent($query)
            ->make(true);
    }
}
