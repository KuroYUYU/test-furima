<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use InvalidArgumentException;

// テストケースNO11 実施のため作成
// 実際のテストはUnit/PaymentMethodSelectionTestに記載しています

class OrderPaymentMethodService
{
    public function createOrder(User $buyer, Product $product, int $paymentMethod, array $shipping): Order
    {
        $status = match ($paymentMethod) {
            1 => Order::STATUS_PAID, // コンビニ支払い
            2 => Order::STATUS_PENDING, // カード払い
            default   => throw new InvalidArgumentException('Unknown payment method: ' . $paymentMethod),
        };

        return Order::create([
            'user_id'           => $buyer->id,
            'product_id'        => $product->id,
            'price'             => $product->price,
            'payment_method'    => $paymentMethod,
            'status'            => $status,
            'shipping_postcode' => $shipping['postcode'],
            'shipping_address'  => $shipping['address'],
            'shipping_building' => $shipping['building'] ?? null,
            'shipping_name'     => $shipping['name'],
        ]);
    }
}
