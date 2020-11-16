<?php

namespace App\Http\Controllers\Manage\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveController extends Controller
{
    public function index($account, Request $request)
    {
        $manage = Auth::guard('manage')->user();
        $payment = '';
        if ($request->has('payment1')) {
            $payment .= $request['payment1'].',';
        }
        if ($request->has('payment2')) {
            $payment .= $request['payment2'].',';
        }

        if ($request->has('action') && $request->has('shops_id')) {
            try {
                DB::table('shops')->where('id', $request['shops_id'])->update([
                    'name' => $request['name'],
                    'tel' => $request['tel'],
                    'fax' => $request['fax'],
                    'email' => $request['email'],
                    'zipcode' => $request['zipcode'],
                    'pref' => $request['pref'],
                    'address1' => $request['address1'],
                    'address2' => $request['address2'],
                    'access' => $request['access'],
                    'googlemap_url' => $request['googlemap_url'],
                    'payment' => $payment,
                    'parking' => $request['parking'],
                    // 営業時間
                    'takeout_sun' => isset($request['takeout_sun']) ? $request['takeout_sun'] : null,
                    'takeout_mon' => isset($request['takeout_mon']) ? $request['takeout_mon'] : null,
                    'takeout_tue' => isset($request['takeout_tue']) ? $request['takeout_tue'] : null,
                    'takeout_wed' => isset($request['takeout_wed']) ? $request['takeout_wed'] : null,
                    'takeout_thu' => isset($request['takeout_thu']) ? $request['takeout_thu'] : null,
                    'takeout_fri' => isset($request['takeout_fri']) ? $request['takeout_fri'] : null,
                    'takeout_sat' => isset($request['takeout_sat']) ? $request['takeout_sat'] : null,
                    'takeout_preparation' => isset($request['takeout_preparation']) ? $request['takeout_preparation'] : 30,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                session()->flash('message', '店舗情報が更新されました。');
            } catch (\Throwable $th) {
                session()->flash('error', 'エラーが発生しました。');
            }
            return redirect()->route('manage.shop.index', ['account' => $account]);
        } else {
            try {
                DB::table('shops')->insert([
                    'manages_id' => $manage->id,
                    'name' => $request['name'],
                    'tel' => $request['tel'],
                    'fax' => $request['fax'],
                    'email' => $request['email'],
                    'zipcode' => $request['zipcode'],
                    'pref' => $request['pref'],
                    'address1' => $request['address1'],
                    'address2' => $request['address2'],
                    'access' => $request['access'],
                    'googlemap_url' => $request['googlemap_url'],
                    'payment' => $payment,
                    'parking' => $request['parking'],
                    // 営業時間
                    'takeout_sun' => isset($request['takeout_sun']) ? $request['takeout_sun'] : null,
                    'takeout_mon' => isset($request['takeout_mon']) ? $request['takeout_mon'] : null,
                    'takeout_tue' => isset($request['takeout_tue']) ? $request['takeout_tue'] : null,
                    'takeout_wed' => isset($request['takeout_wed']) ? $request['takeout_wed'] : null,
                    'takeout_thu' => isset($request['takeout_thu']) ? $request['takeout_thu'] : null,
                    'takeout_fri' => isset($request['takeout_fri']) ? $request['takeout_fri'] : null,
                    'takeout_sat' => isset($request['takeout_sat']) ? $request['takeout_sat'] : null,
                    'takeout_preparation' => isset($request['takeout_preparation']) ? $request['takeout_preparation'] : 30,

                    'updated_at' => now(),
                ]);
                session()->flash('message', '店舗情報が更新されました。');
            } catch (\Throwable $th) {
                session()->flash('error', 'エラーが発生しました。');
            }
            return redirect()->route('manage.shop.index', ['account' => $account]);
        }
    }
}
