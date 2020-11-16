<?php

namespace App\Http\Controllers\Manage\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index($account, $id)
    {
        $order = DB::table('orders')->find($id);
        $shop = DB::table('shops')->find($order->shops_id);

        $products = [];
        foreach (json_decode($order->carts) as $data) {
            $product = DB::table('products')->find($data->product_id);
            $options = [];
            $amount = 0;
            $amount += $product->price;
            foreach ($data->options as $key => $opt_id) {
                $opt = DB::table('options')->find($opt_id);
                $options[] = [
                    'name' => $opt->name,
                    'price' => $opt->price,
                ];
                $amount += $opt->price;
            }
            $amount *= (int)$data->quantity;
            $products[] = [
                'name' => $product->name,
                'thumbnail' => $product->thumbnail_1,
                'quantity' => (int)$data->quantity,
                'amount' => $amount,
                'options' => $options,
            ];
        }

        // dd($order);

        return view('manage.order.detail', [
            'order' => $order,
            'shop' => $shop,
            'products' => $products,
        ]);
    }

    public function cancel($account, $id, Request $request)
    {
        $orders = DB::table('orders')->find($id);

        // キャンセルフラグ処理
        try {
            DB::table('orders')
            ->where('id', $id)
            ->update([
                'cancel' => 1,
                'updated_at' => now(),
            ]);
            session()->flash('message', 'キャンセル処理が完了しました。');
        } catch (\Throwable $th) {
            session()->flash('error', 'システムエラーが発生しました。');
        }

        // クレカ返金処理
        if ($orders->charge_id != null && $orders->charge_id != '') {
            try {
                \Payjp\Payjp::setApiKey(config('app.payjpkey'));
                $ch = \Payjp\Charge::retrieve($orders->charge_id);
                $ch->refund();
            } catch (\Throwable $th) {
                session()->flash('error', 'クレジットカード決済の返金に失敗しました。');
            }
        }

        return redirect()->route('manage.order.detail', ['account' => $account, 'id' => $id]);
    }
}
