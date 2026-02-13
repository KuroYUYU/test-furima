<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Product $product)
    {
        // コメント保存
        Comment::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'body'       => $request->input('body'),
        ]);

        // 商品詳細へ戻す
        return redirect()->back();
    }
}
