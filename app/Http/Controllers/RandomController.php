<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        if (!$title || !$content) {
            $request->session()->flash('alert-warning', 'Введите заголовок и текст письма!');
            return redirect('/random/email');
        }


        dd($request->all());
    }
}
