<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\CouponLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HeyyyController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('coupons.index');
    }

    public function getCoupon()
    {
        $result = DB::table('coupons')->whereNotIn('id', function($q) {
            $q->select('coupon_id')->from('coupon_logs');
        })->first();

        return response()->json($result);
    }

    public function save(Request $request)
    {
        $params = $request->all();

        if (empty($params['coupon-id'])) {
            return response()->json(['error' => 'Something went wrong(((']);
        }

        $couponLog = new CouponLog([
            'coupon_id' => $params['coupon-id'],
            'status' => CouponLog::STATUS_DONE,
            'user_id' => Auth::user()->id
        ]);

        $couponLog->save();

        return response()->json(['status' => 'ok']);
    }
}
