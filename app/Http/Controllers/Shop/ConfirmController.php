<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfirmController extends Controller
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

        if (!$request->session()->has(['receipt', 'cart'])) {
            redirect()->route('shop.home', ['account' => $account]);
        }

        if (Auth::check()) {
            $point_flag = $manages->point_flag;
        } else {
            $point_flag = 0;
        }

        $receipt = session('receipt'); // 受け取り設定
        if (isset($receipt['shop_id'])) {
            $shop = DB::table('shops')->where('id', $receipt['shop_id'])->first(); // 店舗情報
        } else {
            $shop = null;
        }
        $cart = session('cart'); // カート情報
        $form_order = session('form_order'); // お客様情報

        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            if ($request['pay'] == 0) {
                Validator::make($request->all(), [
                    'payjp-token' => 'required',
                ])->validate();
            }

            if (Auth::check()) {
                Validator::make($request->all(), [
                    'pay' => 'max:'.$point_flag,
                ])->validate();
            }

            if ($request->has('use_points') && $request->use_points > 0) {
                $user_id = Auth::id();
                $points = DB::table('points')->where(['manages_id' => $manages->id, 'users_id' => $user_id])->first();
                if ($points == null) {
                    $points = 0;
                } else {
                    $points = $points->count;
                }
                Validator::make($request->all(), [
                    'use_points' => 'required',
                ])->validate();
            }

            $request->session()->put('form_payment', $request->all());
            $payment = $request->all(); // 支払い情報
        } else {
            $payment = session('form_payment'); // 支払い情報
        }

        $carts = [];
        $amount = 0;
        foreach ($cart['products'] as $product) {
            $item = DB::table('products')->find($product['id']);
            $price = $item->price;
            $options = [];
            $options_id = '';
            if (is_array($product['options'])) {
                foreach ($product['options'] as $option) {
                    $opt_temp = DB::table('options')->find($option);
                    $price += $opt_temp->price;
                    $options[] = $opt_temp->name;
                    $options_id .= $option.',';
                }
            }
            $carts[] = [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $product['quantity'],
                'price' => $price * (int)$product['quantity'],
                'options' => $options,
                'options_id' => $options_id,
            ];
            $amount += $price * (int)$product['quantity']; // 合計金額
        }

        return view('shop.confirm', [
            'receipt' => $receipt,
            'shop' => $shop,
            'carts' => $carts,
            'form_order' => $form_order,
            'payment' => $payment,
            'amount' => $amount,
            'point_flag' => $point_flag,
        ]);
    }
}
