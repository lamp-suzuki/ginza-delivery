<?php

namespace App\Http\Controllers\Manage\Product\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        try {
            $manage = Auth::guard('manage')->user();
            $categories = DB::table('categories')->where([
                ['manages_id', $manage->id],
            ])->orderBy('sort_id', 'asc')->get();
            if ($categories == null) {
                $categories = [];
            }
        } catch (\Exception $e) {
            $categories = [];
        }

        return view('manage.product.category.index', [
            'categories' => $categories
        ]);
    }

    // カテゴリ追加
    public function add($account, Request $request)
    {
        try {
            DB::table('categories')->insert([
                'manages_id' => $request['manage_id'],
                'name' => $request['category_name'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            session()->flash('message', 'カテゴリが追加されました。');
        } catch (\Exception $e) {
            session()->flash('error', 'エラーが発生しました。');
        }

        return redirect()->route('manage.product.category.index', ['account' => $account]);
    }

    // カテゴリ編集
    public function edit($account, Request $request)
    {
        try {
            DB::table('categories')
                ->where('id', $request['category_id'])
                ->update([
                    'name' => $request['category_name'],
                    'updated_at' => now(),
                ]);
            session()->flash('message', 'カテゴリが編集されました。');
        } catch (\Exception $e) {
            session()->flash('error', 'エラーが発生しました。');
        }

        return redirect()->route('manage.product.category.index', ['account' => $account]);
    }

    // カテゴリ削除
    public function delete($account, Request $request)
    {
        try {
            $products_ids = DB::table('products')->where('categories_id', $request['category_id'])->pluck('id');
            foreach ($products_ids as $id) {
                DB::table('stocks')->where('products_id', $id)->delete();
                DB::table('stock_customers')->where('products_id', $id)->delete();
            }
            DB::table('products')->where('categories_id', $request['category_id'])->delete();
            DB::table('options')->where('categories_id', $request['category_id'])->delete();
            DB::table('categories')->where('id', $request['category_id'])->delete();
            session()->flash('message', 'カテゴリが削除されました。');
        } catch (\Exception $e) {
            dd($e);
            session()->flash('error', 'エラーが発生しました。');
        }

        return redirect()->route('manage.product.category.index', ['account' => $account]);
    }

    // 並び替え
    public function sort_cat(Request $request)
    {
        $sorted = $request['sort_ids'];
        try {
            foreach ($sorted as $index => $id) {
                DB::table('categories')
                ->where('id', $id)
                ->update([
                    'sort_id' => $index
                ]);
            }
            return "OK";
        } catch (\Throwable $th) {
            return "NO";
        }
    }
}
