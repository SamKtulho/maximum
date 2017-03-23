<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Email;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class DomainController extends Controller
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
        return view('domain.create');
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
            $string = trim($string);
            $domainString = trim(strstr($string, ' ', true));
            if (!$domainString) continue;
            $emails = explode(',', trim(strstr($string, ' ')));

            $domain = new Domain;
            $domain->domain = $domainString;
            $domain->tic = (int) $request->get('tic', 10);
            $domain->status = Domain::STATUS_NOT_PROCESSED;

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
}
