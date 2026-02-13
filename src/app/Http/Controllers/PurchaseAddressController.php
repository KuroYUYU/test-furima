<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Http\Requests\AddressRequest;

class PurchaseAddressController extends Controller
{
    public function edit(Product $product)
    {
        return view('purchase.address', compact('product'));
    }

    public function store(AddressRequest $request, Product $product)
    {
        session(['purchase.shipping' => [
            'product_id' => $product->id,
            'postcode'  => $request->postcode,
            'address'   => $request->address,
            'building'  => $request->building,
        ]]);

        return redirect()->route('purchase.create', $product);
    }
}
