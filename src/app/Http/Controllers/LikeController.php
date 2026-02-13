<?php

namespace App\Http\Controllers;
use App\Models\Product;

class LikeController extends Controller
{
    public function store(Product $product)
    {
        $product->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Product $product)
    {
        $product->likes()
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['ok' => true]);
    }
}
