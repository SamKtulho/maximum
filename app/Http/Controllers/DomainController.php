<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Link;
use App\Models\Email;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class DomainController extends Controller
{
    private $title = 'Домены';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_user');
        $this->middleware('is_admin');
    }
    
    public function create()
    {
        return view('domain.create', ['title' => $this->title . ' -> добавить']);
    }

    public function store(Request $request)
    {
        $content = $request->get('content', null);
        if (empty($content)) {
            $request->session()->flash('alert-warning', 'Пустой контент');
            return back();
        }
        $content = explode(PHP_EOL, $content);

        $errorsCount = $successCount = 0;
        foreach ($content as $string) {
            $domainString = trim(strtolower($string));
            if (!$domainString) continue;
            $domain = new Domain;
            $domain->domain = $domainString;
            $domain->tic = (int) $request->get('tic', 10);
            $domain->source = $request->get('source', null);
            $domain->status = Domain::STATUS_MODERATE;
            $domain->type = Domain::TYPE_UNKNOWN;

            try {
                if ($domain->save()) {
                    ++$successCount;
                }
            } catch (\Exception $e) {
                ++$errorsCount;
                continue;
            }
        }

        if ($successCount) {
            $request->session()->flash('alert-success', 'Успешно добавлено ' . $successCount . ' доменов.');
        }

        if ($errorsCount) {
            $request->session()->flash('alert-danger', 'С ошибкой ' . $errorsCount . ' доменов.');
        }

        return redirect('/domain/create');
    }

    public function _OLD_store(Request $request)
    {
        $content = $request->get('content', null);
        if (empty($content)) {
            $request->session()->flash('alert-warning', 'Пустой контент');
            return back();
        }
        $content = explode(PHP_EOL, $content);

        $errorsCount = $successCount = 0;
        foreach ($content as $string) {
            $string = trim($string);
            $domainString = trim(strstr($string, ' ', true));
            if (!$domainString) continue;
            $emails = explode(',', trim(strstr($string, ' ')));

            $domain = new Domain;
            $domain->domain = $domainString;
            $domain->tic = (int) $request->get('tic', 10);
            $domain->source = $request->get('source', null);
            $domain->status = Domain::STATUS_MODERATE;

            try {
                if ($domain->save()) {
                    foreach ($emails as $email) {
                        $emailModel = Email::where('email', $email)->first();
                        if ($emailModel) {
                         //   $storedDomain = Domain::where('id', $emailModel->domain_id)->first(); // ТИЦ отключили пока
                         //   if ($storedDomain && $domain->tic > $storedDomain->tic) { // ТИЦ отключили пока
                                $emailModel->domain_id = $domain->id;
                                $emailModel->save();
                         //   } // ТИЦ отключили пока
                        } else {
                            $emailModel = new Email();
                            foreach ($emailModel->getStopWords() as $stopWord) {
                                if (strpos($email, $stopWord) !== false) continue;
                            }
                            $emailModel->email = $email;
                            $emailModel->domain_id = $domain->id;
                            $emailModel->is_valid = Email::STATUS_NOT_VALID;
                            $emailModel->save();
                        }
                    }
                    ++$successCount;
                }
            } catch (\Exception $e) {
                ++$errorsCount;
                continue;
            }
        }

        if ($successCount) {
            $request->session()->flash('alert-success', 'Успешно добавлено ' . $successCount . ' доменов.');
        }

        if ($errorsCount) {
            $request->session()->flash('alert-danger', 'С ошибкой ' . $errorsCount . ' доменов.');
        }

        return redirect('/domain/create');
    }
    
    public function back(Request $request)
    {
        $domain = $request->get('domain');
        if (empty($domain) || !($domain = Domain::where('domain', $domain)->first())) {
            return response()->json(['error' => 'Такого домена не существует']);
        }
        $domain->status = Domain::STATUS_NOT_PROCESSED;
        $domain->save();
        $link = $domain->links()->first();
        $link->status = Link::STATUS_NOT_PROCESSED;
        $link->save();

        return response()->json(['response' => 'Домен успешно помечен как необработанный']);

    }
}
