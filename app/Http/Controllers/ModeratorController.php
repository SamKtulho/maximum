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
        $this->middleware('is_user');

    }

    public function link()
    {
        return view('moderator.link', ['title' => 'Регистраторы -> модератор']);
    }

    public function email()
    {
        return view('moderator.email', ['title' => 'Письма -> модератор']);
    }

    public function subdomain()
    {
        return view('moderator.subdomain', ['title' => 'Субдомены -> модератор']);
    }

    public function vote(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domainId = (int) $request->get('domain_id');
        $isSkipped = (int) !$request->get('is_active');

        if ($vote && $domainId) {
            $domain = Domain::find($domainId);
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_MANUAL_CHECK : Domain::STATUS_BAD;
                $domain->save();
                $moderationLog = new ModerationLog();
                $moderationLog->domain_id = $domainId;
                $moderationLog->result = $vote === self::VOTE_YES ? ModerationLog::RESULT_YES : ModerationLog::RESULT_NO;
                $moderationLog->type = ModerationLog::TYPE_LINK;
                $moderationLog->user_id = Auth::user()->id;
                $moderationLog->is_skipped = $isSkipped;
                $moderationLog->save();
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

        $statDB = DB::table('moderation_logs')
            ->where('user_id', Auth::user()->id)
            ->where('type', ModerationLog::TYPE_LINK);


        return response()->json(['response' => ['domain' => $domain, 'count' => $count, 'user_count' => $statDB->count()]]);
    }

    public function changeVote(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domain = $request->get('domain');

        if ($vote && $domain) {
            $domain = Domain::where('domain', $domain)->first();
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_MANUAL_CHECK : Domain::STATUS_BAD;
                $domain->save();
                $moderationLog = ModerationLog::where('domain_id', $domain->id)->first();
                $moderationLog->result = $vote === self::VOTE_YES ? ModerationLog::RESULT_YES : ModerationLog::RESULT_NO;
                $moderationLog->save();
                return response()->json(['response' => 'ok']);

            }
        }
        return response()->json(['error' => 'error']);
    }

    public function voteEmail(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domainId = (int) $request->get('domain_id');
        $isSkipped = (int) !$request->get('is_active');

        if ($vote && $domainId) {
            $domain = Domain::find($domainId);
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_NOT_PROCESSED : Domain::STATUS_BAD;
                $domain->save();
                $moderationLog = new ModerationLog();
                $moderationLog->domain_id = $domainId;
                $moderationLog->result = $vote === self::VOTE_YES ? ModerationLog::RESULT_YES : ModerationLog::RESULT_NO;
                $moderationLog->type = ModerationLog::TYPE_EMAIL;
                $moderationLog->user_id = Auth::user()->id;
                $moderationLog->is_skipped = $isSkipped;
                $moderationLog->save();
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

        $statDB = DB::table('moderation_logs')
            ->where('user_id', Auth::user()->id)
            ->where('type', ModerationLog::TYPE_EMAIL);


        return response()->json(['response' => ['domain' => $domain, 'count' => $count, 'user_count' => $statDB->count()]]);
    }

    public function voteSubdomain(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domainId = (int) $request->get('domain_id');
        $isSkipped = (int) !$request->get('is_active');

        if ($vote && $domainId) {
            $domain = Domain::find($domainId);
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_MANUAL_CHECK : Domain::STATUS_BAD;
                $domain->save();
                $moderationLog = new ModerationLog();
                $moderationLog->domain_id = $domainId;
                $moderationLog->result = $vote === self::VOTE_YES ? ModerationLog::RESULT_YES : ModerationLog::RESULT_NO;
                $moderationLog->type = ModerationLog::TYPE_SUBDOMAIN;
                $moderationLog->user_id = Auth::user()->id;
                $moderationLog->is_skipped = $isSkipped;
                $moderationLog->save();
            }
        }

        $domainDB = DB::table('domains')
            ->where('domains.status', Domain::STATUS_MODERATE)
            ->where('domains.type', Domain::TYPE_SUBDOMAIN);

        $domain = $domainDB->first();
        $count = $domainDB->count();

        $statDB = DB::table('moderation_logs')
            ->where('user_id', Auth::user()->id)
            ->where('type', ModerationLog::TYPE_SUBDOMAIN);

        return response()->json(['response' => ['domain' => $domain, 'count' => $count, 'user_count' => $statDB->count()]]);
    }
    
    public function report()
    {
        $daysBefore = 7;
        $range = [];
        for ($i = 0; $i < $daysBefore; ++$i) {
            $range[] = strtotime(date('Y-m-d', strtotime('-' . $i . ' days')));
        }

        $statDBUser = DB::table('moderation_logs')
            ->join('users', 'users.id', '=', 'moderation_logs.user_id')
            ->select('user_id', 'type', 'users.name', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->groupBy('type')
            ->get();

        $statDBDate = DB::table('moderation_logs')
            ->where('created_at', '>', date('Y-m-d', strtotime('-'. $daysBefore . ' days')))
            ->select('type', 'created_at', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->groupBy('created_at')
            ->get()->toArray();

        $resultByDate = [];

        foreach ($statDBDate as $stat) {
            $statDate = strtotime(str_limit($stat->created_at, 10, ''));
            $resultByDate[$stat->type][$statDate] =
                isset($resultByDate[$stat->type][$statDate])
                    ? $resultByDate[$stat->type][$statDate] + $stat->total
                    : $stat->total;
        }


        foreach ($resultByDate as &$result) {
            foreach ($range as $date) {
                if (!isset($result[$date])) {
                    $result[$date] = 0;
                }
             }
            krsort($result);
        }

        unset($result);

        return view('moderator.report', ['reportByUser' => $statDBUser, 'reportByDate' => $resultByDate]);
    }
}
