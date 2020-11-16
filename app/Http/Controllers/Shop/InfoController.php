<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // お知らせ詳細
    public function index($account, $id)
    {
        $news = DB::table('posts')->find($id);
        return view('shop.newsdetail', [
            'news' => $news,
        ]);
    }

    // 店舗詳細
    public function shopinfo($account, $id)
    {
        $shops = DB::table('shops')->find($id);
        $manages = DB::table('manages')->find($shops->manages_id);
        return view('shop.shopinfo', [
            'manages' => $manages,
            'shops' => $shops,
        ]);
    }

    // ご利用ガイド
    public function guide($account)
    {
        $sub_domain = $account;
        $manages = DB::table('manages')->where('domain', $sub_domain)->first();
        $guide = DB::table('guides')->where('manages_id', $manages->id)->first();
        return view('shop.guide', [
            'guide' => $guide
        ]);
    }
}
