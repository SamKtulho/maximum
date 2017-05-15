<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use App\Models\Shorturl;
use App\Models\ModerationLog;
use Yajra\Datatables\Datatables;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_user');
        $this->middleware('is_moderator');
    }

    public function create()
    {
        return view('email.create');
    }

    public function store(Request $request)
    {
        $content = $request->get('content', null);
        if (empty($content)) return back()->with('message', 'Пустой контент');
        $content = explode(PHP_EOL, $content);
        
        $errorsCount = $successCount = 0;
        foreach ($content as $string) {
            $string = trim($string);
            $emailString = trim(strstr($string, ' ', true));
            if (!$emailString) continue;
            $status = trim(strstr($string, ' '));

            if ($status === 'Невалидный') {
                ++$errorsCount;
                continue;
            }

            $emailModel = Email::where('email', $emailString)->first();

            if ($emailModel) {
                $emailModel->is_valid = Email::STATUS_VALID;
                $emailModel->save();
                ++$successCount;
            }
        }
        if ($successCount) {
            $request->session()->flash('alert-success', 'Валидных емайлов ' . $successCount . '.');
        }

        if ($errorsCount) {
            $request->session()->flash('alert-danger', 'Невалидных емайлов ' . $errorsCount . '.');
        }
        return redirect('/email/create');
    }

    public function data(Datatables $datatables)
    {
        $query = Shorturl::with('user')->with('domain')->with('urlstats')->select('shorturls.*')->whereIn('type', [Shorturl::TYPE_GOOGLE, Shorturl::TYPE_OTHER]);

        return $datatables->eloquent($query)
            ->addColumn('stat', function ($shorturl) {
                return isset($shorturl->urlstats[0]) ? unserialize($shorturl->urlstats[0]->stat)['allTime']['shortUrlClicks'] : '?';
            })
            ->addColumn('email', function ($shorturl) {
                return isset($shorturl->domain->emails[0]) ? $shorturl->domain->emails[0]->email : '';
            })
            ->make(true);
    }
    
    public function statistic()
    {
        return view('email.statistic', ['shortUrls' => []]);
    }
    
    public function count()
    {
        return response()->json(['response' => \App\Services\Email::getEmailsCount()]);
    }

    public function moderationLog()
    {
        return view('email.moderation_log', ['logs' => []]);
    }

    public function moderationLogData(Datatables $datatables)
    {
        $query = ModerationLog::with('user')->with('domain')->where('moderation_logs.type', ModerationLog::TYPE_EMAIL);

        return $datatables->eloquent($query)
            ->make(true);
    }
}
