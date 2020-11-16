<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($account, Request $request)
    {
        $sub_domain = $account;
        $manages = DB::table('manages')->where('domain', $sub_domain)->first();

        if ($request->has('member_check') && $request->member_check == 1) {
            $auth_users = DB::table('users')->where('email', $request->email)->count();
            if ($auth_users >= 1) {
                session()->flash('message', '既に会員登録済みのメールアドレスです。');
                return redirect()->route('shop.order', ['account' => $account]);
            }
        }

        if (Auth::check()) {
            $point_flag = $manages->point_flag;
            $user_id = Auth::id();
            $points = DB::table('points')->where(['manages_id' => $manages->id, 'users_id' => $user_id])->first();
            if ($points == null) {
                $points = 0;
            } else {
                $points = $points->count;
            }
        } else {
            $point_flag = 0;
            $points = 0;
        }

        if ($_SERVER["REQUEST_METHOD"] != 'GET') {
            Validator::make($request->all(), [
                'name1' => 'required',
                'name2' => 'required',
                'furi1' => 'required',
                'furi2' => 'required',
                'email' => 'required|email',
                'email' => 'required|email|confirmed',
                'tel' => 'required',
                'zipcode' => 'alpha_dash',
            ])->validate();
            $request->session()->put('form_order', $request->all());
        }

        if (session('receipt.service') == 'takeout' && session('receipt.shop_id') != null) {
            $shops = DB::table('shops')->find(session('receipt.shop_id'));
        } else {
            $shops = [];
        }

        // dd($shops);

        return view('shop.payment', [
            'point_flag' => $point_flag,
            'points' => $points,
            'shops' => $shops,
        ]);
    }
}
