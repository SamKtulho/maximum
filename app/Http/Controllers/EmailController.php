<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use App\Models\Shorturl;
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
        return $datatables->eloquent(Shorturl::where('type', Shorturl::TYPE_GOOGLE)->orwhere('type', Shorturl::TYPE_OTHER))
            ->addColumn('domain', function ($shorturl) {
                return '<a target="_blank" href="//' . $shorturl->domain->domain . '">' . $shorturl->domain->domain . '</a>';
            })
            ->addColumn('stat', function ($shorturl) {
                return isset($shorturl->urlstats[0]) ? unserialize($shorturl->urlstats[0]->stat)['allTime']['shortUrlClicks'] : '?';
            })
            ->addColumn('email', function ($shorturl) {
                return $shorturl->domain->emails[0]->email;
            })
            ->addColumn('user', function ($shorturl) {
                return $shorturl->user->name;
            })
            ->rawColumns(['domain'])
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
}
