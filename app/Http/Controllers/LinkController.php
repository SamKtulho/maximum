<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Domain;
use App\Models\Shorturl;


class LinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        return view('link.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
        ]);

        $content = $request->get('content', null);

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
            $domain->status = Domain::STATUS_NOT_PROCESSED;

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
    public function statistic()
    {
        $shortUrls = Shorturl::where('type', Shorturl::TYPE_REGISTRAR)->get();
        return view('link.statistic', ['shortUrls' => $shortUrls]);
    }

    public function count()
    {
        return response()->json(['response' => \App\Services\Link::getLinksCount()]);
    }

}
