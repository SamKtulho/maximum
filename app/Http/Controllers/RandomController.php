<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Random;

class RandomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function email(Request $request)
    {
        return view('random.email');
    }

    public function emailStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('edomain');
        $tic = $request->get('tic');
        $isSkip = (bool) $request->get('skip', false);

        if (!$title || !$content || !$domain) {
            return response()->json(['error' => 'Введите заголовок, текст письма и хотябы 1 почтовый домен!']);
        }

        $data = Random::prepareData($content, $title, $domain, $tic, $isSkip);
        return response()->json(['response' => $data]);
    }
}
