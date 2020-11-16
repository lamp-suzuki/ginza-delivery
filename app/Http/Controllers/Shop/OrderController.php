<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($account, Request $request)
    {
        $manages = DB::table('manages')->where('domain', $account)->first();
        $point_flag = $manages->point_flag;
        $request->session()->put('form_cart', $request->all());
        if ($request['okimochi'] != 0) {
            session()->put('cart.okimochi', (int)$request['okimochi']);
        }

        if ($request->has('email') && $request->has('password')) { // ログイン時
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials) == true) {
                $users = Auth::guard('web')->user();
                return view('shop.order', [
                    'users' => $users,
                ]);
            } elseif (Auth::attempt($credentials) == false) {
                session()->flash('error', 'ログインできませんでした。入力項目をご確認の上、再度お試しください。');
                return redirect()->route('shop.cart', ['account' => $account]);
            }
        } elseif (Auth::check('web')) { // 既にログイン済
            $users = Auth::guard('web')->user();
            return view('shop.order', [
                'users' => $users,
            ]);
        } else {
            return view('shop.order', [
                'point_flag' => $point_flag,
            ]);
        }
    }
}
