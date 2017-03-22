<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;

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
        return view('moderator.link');
    }

    public function vote(Request $request)
    {
        $vote = (int) $request->get('vote');
        $domainId = (int) $request->get('domain_id');
        
        if ($vote && $domainId) {
            $domain = Domain::find($domainId);
            if ($domain) {
                $domain->status = $vote === self::VOTE_YES ? Domain::STATUS_NOT_PROCESSED : Domain::STATUS_BAD;
                $domain->save();
            }
        }

        $domain = Domain::where('type', Domain::TYPE_LINK)->where('status', Domain::STATUS_MODERATE)->first();

        return response()->json(['response' => $domain]);
    }
}
