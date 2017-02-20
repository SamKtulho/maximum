<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Random;

class RandomController extends Controller
{
    public function email(Request $request)
    {
        return view('random.email');
    }

    public function emailStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('edomain');

        if (!$title || !$content || !$domain) {
            $request->session()->flash('alert-warning', 'Введите заголовок, текст письма и хотябы 1 почтовый домен!');
            return redirect('/random/email');
        }

        dd($request->all());
    }
}
