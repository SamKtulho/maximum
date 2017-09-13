<?php

namespace App\Http\Controllers;

use App\Models\Shorturl;
use Illuminate\Http\Request;
use App\Services\Random;
use App\Models\Template;

class RandomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_user');

    }
    
    public function email(Request $request)
    {
        $emailTemplate = Template::where('type', Template::TYPE_EMAIL)->first();
        $template = [];
        if ($emailTemplate) {
            $template = unserialize($emailTemplate->template);
        }
        return view('random.email', ['template' => $template, 'title' => 'Письма -> отправка']);
    }

    public function link(Request $request)
    {
        $linkTemplate = Template::where('type', Template::TYPE_LINK)->first();
        $template = [];
        if ($linkTemplate) {
            $template = unserialize($linkTemplate->template);
        }
        return view('random.link', ['template' => $template, 'title' => 'Регистраторы -> отправка']);
    }

    public function manualDomain(Request $request)
    {
        $linkTemplate = Template::where('type', Template::TYPE_MANUAL_DOMAIN)->first();
        $template = [];
        if ($linkTemplate) {
            $template = unserialize($linkTemplate->template);
        }
        return view('random.manual', ['template' => $template, 'title' => 'Регистраторы -> поиск контактов']);
    }

    public function manualSubdomain(Request $request)
    {
        $linkTemplate = Template::where('type', Template::TYPE_MANUAL_SUBDOMAIN)->first();
        $template = [];
        if ($linkTemplate) {
            $template = unserialize($linkTemplate->template);
        }
        return view('random.manualSubdomain', ['template' => $template, 'title' => 'Субдомены -> поиск контактов']);
    }

    public function manualEmail()
    {
        $linkTemplate = Template::where('type', Template::TYPE_MANUAL_EMAIL)->first();
        $template = [];
        if ($linkTemplate) {
            $template = unserialize($linkTemplate->template);
        }
        return view('random.manualEmail', ['template' => $template, 'title' => 'Письма -> поиск контактов']);
    }

    public function emailStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('edomain');
        $tic = $request->get('tic');
        $isSkip = (bool) $request->get('skip', false);
        $isSave = (bool) $request->get('saveTemplate', false);

        if ($isSave) {
            $emailTemplate = Template::where('type', Template::TYPE_EMAIL)->first();
            if (!$emailTemplate) {
                $emailTemplate = new Template();
                $emailTemplate->type = Template::TYPE_EMAIL;
            }
            $emailTemplate->template = serialize(['title' => $title, 'content' => $content]);
            $emailTemplate->save();
            return response()->json(['response' => 'Сохранено']);
        }

        $tic = $tic ? $tic : -1;

        if (!$title || !$content || (!$domain && !$isSkip)) {
            return response()->json(['error' => 'Введите заголовок, текст письма и хотябы 1 почтовый домен!']);
        }

        $data = Random::emailPrepareData($content, $title, $domain, $tic, $isSkip);
        return response()->json(['response' => $data]);
    }

    public function linkStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('ldomain');
        $fio = $request->get('fio');
        $email = $request->get('email');
        $isSkip = (bool) $request->get('skip', false);
        $isSave = (bool) $request->get('saveTemplate', false);

        if ($isSave) {
            $linkTemplate = Template::where('type', Template::TYPE_LINK)->first();
            if (!$linkTemplate) {
                $linkTemplate = new Template();
                $linkTemplate->type = Template::TYPE_LINK;
            }
            $linkTemplate->template = serialize(['title' => $title, 'content' => $content, 'fio' => $fio, 'email' => $email]);
            $linkTemplate->save();
            return response()->json(['response' => 'Сохранено']);
        }

        if (!$title || !$content || !$fio || !$email || (!$domain && !$isSkip)) {
            return response()->json(['error' => 'Введите заголовок, текст письма, ФИО, email и хотябы 1 домен!']);
        }

        $data = Random::linkPrepareData($fio, $email, $title, $content, $domain, $isSkip);

        return response()->json(['response' => $data]);
    }

    public function manualDomainStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('ldomain');
        $fio = $request->get('fio');
        $email = $request->get('email');
        $isSkip = (bool) $request->get('skip', false);
        $isSave = (bool) $request->get('saveTemplate', false);

        if ($isSave) {
            $linkTemplate = Template::where('type', Template::TYPE_MANUAL_DOMAIN)->first();
            if (!$linkTemplate) {
                $linkTemplate = new Template();
                $linkTemplate->type = Template::TYPE_MANUAL_DOMAIN;
            }
            $linkTemplate->template = serialize(['title' => $title, 'content' => $content, 'fio' => $fio, 'email' => $email]);
            $linkTemplate->save();
            return response()->json(['response' => 'Сохранено']);
        }
        if (!$title || !$content || !$fio || !$email || (!$domain && !$isSkip)) {
            return response()->json(['error' => 'Введите заголовок, текст письма, ФИО, email и хотябы 1 регистратор!']);
        }

        $data = Random::manualGenText($fio, $email, $title, $content, $domain, $isSkip);

        $stat = Shorturl::getStatistic(Shorturl::TYPE_REGISTRAR);
        return response()->json(['response' => array_merge($data, ['statistic' => $stat])]);
    }

    public function manualSubdomainStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $fio = $request->get('fio');
        $email = $request->get('email');
        $isSkip = (bool) $request->get('skip', false);
        $isSave = (bool) $request->get('saveTemplate', false);

        if ($isSave) {
            $linkTemplate = Template::where('type', Template::TYPE_MANUAL_SUBDOMAIN)->first();
            if (!$linkTemplate) {
                $linkTemplate = new Template();
                $linkTemplate->type = Template::TYPE_MANUAL_SUBDOMAIN;
            }
            $linkTemplate->template = serialize(['title' => $title, 'content' => $content, 'fio' => $fio, 'email' => $email]);
            $linkTemplate->save();
            return response()->json(['response' => 'Сохранено']);
        }
        if (!$title || !$content || !$fio || !$email) {
            return response()->json(['error' => 'Введите заголовок, текст письма, ФИО, email и хотябы 1 регистратор!']);
        }

        $data = Random::manualSubdomainGenText($fio, $email, $title, $content, $isSkip);

        return response()->json(['response' => $data]);
    }

    public function manualEmailStore(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');
        $domain = $request->get('ldomain');
        $fio = $request->get('fio');
        $email = $request->get('email');
        $isSkip = (bool) $request->get('skip', false);
        $isSave = (bool) $request->get('saveTemplate', false);

        if ($isSave) {
            $linkTemplate = Template::where('type', Template::TYPE_MANUAL_EMAIL)->first();
            if (!$linkTemplate) {
                $linkTemplate = new Template();
                $linkTemplate->type = Template::TYPE_MANUAL_EMAIL;
            }
            $linkTemplate->template = serialize(['title' => $title, 'content' => $content, 'fio' => $fio, 'email' => $email]);
            $linkTemplate->save();
            return response()->json(['response' => 'Сохранено']);
        }
        if (!$title || !$content || !$fio || !$email) {
            return response()->json(['error' => 'Введите заголовок, текст письма, ФИО, email и хотябы 1 регистратор!']);
        }

        $data = Random::manualEmailGenText($fio, $email, $title, $content, $domain, $isSkip);

        $stat = Shorturl::getStatistic(Shorturl::TYPE_GOOGLE);
        return response()->json(['response' => array_merge($data, ['statistic' => $stat])]);
    }
}
