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
        foreach ($content as $string) {
            $string = trim($string);
            $emailString = trim(strstr($string, ' ', true));
            if (!$emailString) continue;
            $status = trim(strstr($string, ' '));

            if ($status === 'Невалидный') continue;

            $emailModel = Email::where('email', $emailString)->first();

            if ($emailModel) {
                $emailModel->is_valid = 1;
                $emailModel->save();
            }
        }
        return redirect('/email/create');
    }
    
    public function statistic()
    {
        $shortUrls = Shorturl::get();

        foreach ($shortUrls as $url) {
            echo $url->url . ' ';
            echo $url->user->name . "<br>";
        }
    }
}
