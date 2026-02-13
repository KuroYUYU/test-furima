<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;


class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $user = \App\Models\User::with('profile')->findOrFail(auth()->id());

        // 初期タブ選択を出品した商品に指定
        $page = $request->query('page', 'sell');

        // sell,buy以外のURLでの入力を防止
        if (in_array($page, ['sell', 'buy'], true)) {
            $page = $page;
        } else {
            $page = 'sell';  // 想定外はsellを表示
        }

        // タブで商品の出品：購入を切り替え
        if ($page === 'sell') {
            $products = Product::where('user_id', $user->id)->with('order')->latest()->get();
        } else {
            $products = Product::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with('order')->latest()->get();
        }

        // プロフィール画像が設定されていれば表示させる
        $path = $user->profile->profile_image_path ?? null;
        $avatarSrc = $path ? asset('storage/' . $path) : null;

        return view('mypage.index', compact('user','products', 'page', 'avatarSrc'));
    }
}
