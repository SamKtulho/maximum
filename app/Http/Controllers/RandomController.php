<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Random;
use App\Models\Template;

class RandomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function email(Request $request)
    {
        $emailTemplate = Template::where('type', Template::TYPE_EMAIL)->first();
        $template = [];
        if ($emailTemplate) {
            $template = unserialize($emailTemplate->template);
        }
        return view('random.email', ['template' => $template]);
    }

    public function emailStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('edomain');
        $tic = $request->get('tic');
        $isSkip = (bool) $request->get('skip', false);
        $isSave = (bool) $request->get('save', false);

        if (!$title || !$content || (!$domain && !$isSkip)) {
            return response()->json(['error' => 'Введите заголовок, текст письма и хотябы 1 почтовый домен!']);
        }

        $data = Random::prepareData($content, $title, $domain, $tic, $isSkip);
        if ($isSave) {
            $emailTemplate = Template::where('type', Template::TYPE_EMAIL)->first();
            if (!$emailTemplate) {
                $emailTemplate = new Template();
                $emailTemplate->type = Template::TYPE_EMAIL;
            }
            $emailTemplate->template = serialize(['title' => $title, 'content' => $content]);
            $emailTemplate->save();
        }
        return response()->json(['response' => $data]);
    }
}
