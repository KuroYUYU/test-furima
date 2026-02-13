<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        // ローカルスコープでキーワード検索
        $keyword = $request->query('keyword');

        $query = Product::query()->with('order')->latest()->keywordSearch($keyword);

        // ログイン中自分の商品表示はしない
        if (auth()->check()) {
            $query->where('user_id', '!=', auth()->id());
        }

        // マイリスト押下 auth「自分がいいねした商品だけ」表示 未ログインなら何も表示しない
        if ($tab === 'mylist') {
            if (!auth()->check()) {
                $products = collect();
                return view('products.index', compact('products', 'tab', 'keyword'));
            }

            $userId = auth()->id();

            $query->whereHas('likes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        $products = $query->get();

        return view('products.index', compact('products','tab','keyword'));
    }

    public function create()
    {
        $conditions = [
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い',
        ];

        $categories = Category::orderBy('id')->get();

        return view('products.sell', compact('conditions', 'categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image')->store('products', 'public');

        // 作成したProductを$productに代入
        $product = Product::create([
            'user_id'     => $request->user()->id,
            'name'        => $request->name,
            'brand_name'  => $request->brand_name,
            'description' => $request->description,
            'price'       => $request->price,
            'condition'   => $request->condition,
            'image_path'  => $path,
        ]);

        $product->categories()->sync($request->category_ids);

        return redirect('/');
    }

    public function show(Product $product)
    {
        $conditions = [
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い',
        ];

        $product->load('categories', 'comments.user.profile');

        // 表示用（新しい順）
        $comments = $product->comments()
            ->with('user.profile')
            ->latest()
            ->get();

        // コメントのカウント
        $commentsCount = $comments->count();

        // いいねのカウント
        $likesCount = $product->likes()->count();

        $isLiked = auth()->check()
            ? $product->likes()->where('user_id', auth()->id())->exists()
            : false;

        // 自分の商品及び売り切れの商品では「購入」ボタンを表示させなくする
        // 未ログインユーザーが購入ボタンを押したら「ログイン画面」に遷移させる
        $isLoggedIn = auth()->check();
        $isOwner = $isLoggedIn && $product->user_id === auth()->id();
        $isSold = $product->order !== null;

        if (!$isLoggedIn) {
            $showBuyButton = true;
            $buyUrl = route('login');
        } elseif (!$isOwner && !$isSold) {
            $showBuyButton = true;
            $buyUrl = route('purchase.create', $product);
        } else {
            $showBuyButton = false;
            $buyUrl = null;
        }

        return view('products.detail', compact('product', 'conditions', 'comments', 'commentsCount', 'likesCount', 'isLiked', 'showBuyButton', 'buyUrl'));
    }
}
