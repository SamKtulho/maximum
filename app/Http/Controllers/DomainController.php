<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Email;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function create()
    {
        return view('domain.create');
    }

    public function store(Request $request)
    {
        $content = $request->get('content', null);
        if (empty($content)) return back()->with('message', 'Пустой контент');
        $content = explode(PHP_EOL, $content);
        foreach ($content as $string) {
            $string = trim($string);
            $domainString = trim(strstr($string, ' ', true));
            if (!$domainString) continue;
            $emails = explode(',', trim(strstr($string, ' ')));

            $domain = new Domain;
            $domain->domain = $domainString;
            $domain->tic = (int) $request->get('tic', 10);
            $domain->status = Domain::STATUS_NOT_PROCESSED;
            if ($domain->save()) {
                foreach ($emails as $email) {
                    $emailModel = Email::where('email', $email)->first();
                    if ($emailModel) {
                        $storedDomain = Domain::where('id', $emailModel->domain_id)->first();
                        if ($storedDomain && $domain->tic >= $storedDomain->tic) {
                            $emailModel->domain_id = $domain->id;
                            $emailModel->save();
                        }
                    } else {
                        $emailModel = new Email();
                        foreach ($emailModel->getStopWords() as $stopWord) {
                            if (strpos($email, $stopWord) !== false) continue;
                        }
                        $emailModel->email = $email;
                        $emailModel->domain_id = $domain->id;
                        $emailModel->is_valid = 0;
                        $emailModel->save();
                    }
                }
            }
        }
    }
}
