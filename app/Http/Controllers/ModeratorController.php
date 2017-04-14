<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\ModerationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ModeratorController extends Controller
{
    const VOTE_YES = 1;
    const VOTE_NO = 2;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function link()
    {
        return view('moderator.link', ['title' => 'Регистраторы -> модератор']);
    }

    public function email()
    {
        return view('moderator.email', ['title' => 'Письма -> модератор']);
    }

    public function vote(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domainId = (int) $request->get('domain_id');
        
        if ($vote && $domainId) {
            $domain = Domain::find($domainId);
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_MANUAL_CHECK : Domain::STATUS_BAD;
                $domain->save();
                $migrationLog = new ModerationLog();
                $migrationLog->domain_id = $domainId;
                $migrationLog->result = $vote === self::VOTE_YES ? ModerationLog::RESULT_YES : ModerationLog::RESULT_NO;
                $migrationLog->type = ModerationLog::TYPE_LINK;
                $migrationLog->user_id = Auth::user()->id;
                $migrationLog->save();
            }
        }

        $domainDB = DB::table('domains')
            ->join('links', 'domains.id', '=', 'links.domain_id')
            ->where('domains.status', Domain::STATUS_MODERATE)
            ->where('domains.type', Domain::TYPE_LINK)
            ->where('links.status', Link::STATUS_NOT_PROCESSED)
            ->whereNotNull('links.registrar')
            ->select('domains.*', 'links.registrar');

        $domain = $domainDB->first();
        $count = $domainDB->count();

        return response()->json(['response' => ['domain' => $domain, 'count' => $count]]);
    }

    public function voteEmail(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domainId = (int) $request->get('domain_id');

        if ($vote && $domainId) {
            $domain = Domain::find($domainId);
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_NOT_PROCESSED : Domain::STATUS_BAD;
                $domain->save();
                $migrationLog = new ModerationLog();
                $migrationLog->domain_id = $domainId;
                $migrationLog->result = $vote === self::VOTE_YES ? ModerationLog::RESULT_YES : ModerationLog::RESULT_NO;
                $migrationLog->type = ModerationLog::TYPE_EMAIL;
                $migrationLog->user_id = Auth::user()->id;
                $migrationLog->save();
            }
        }

        $domainDB = DB::table('domains')
            ->join('emails', 'domains.id', '=', 'emails.domain_id')
            ->where('domains.status', Domain::STATUS_MODERATE)
            ->where('domains.type', Domain::TYPE_EMAIL)
            ->where('emails.is_valid', 1)
            ->select('domains.*', 'emails.email');

        $domain = $domainDB->first();
        $count = $domainDB->count();

        return response()->json(['response' => ['domain' => $domain, 'count' => $count]]);
    }
}
