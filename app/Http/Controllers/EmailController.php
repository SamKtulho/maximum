<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use App\Models\Shorturl;

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
    
    public function statistic()
    {
        $shortUrls = Shorturl::where('type', Shorturl::TYPE_GOOGLE)->orwhere('type', Shorturl::TYPE_OTHER)->get();
        return view('email.statistic', ['shortUrls' => $shortUrls]);
    }
    
    public function count()
    {
        return response()->json(['response' => \App\Services\Email::getEmailsCount()]);
    }
}
