<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Services\OrderPaymentMethodService;

class PaymentMethodSelectionTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use RefreshDatabase;

    // このテストの実行に際し、App\Services\OrderPaymentMethodServiceに情報を記載
    // 運営に確認しDBまでの反映とのことでコンビニとカード両方のテストを実施
    // 11 : 支払い方法選択機能

    // Case1 小計画面で変更が反映される ※コンビニ払いを選択した時
    public function test_selected_payment_method_is_konbini()
    {
        // 前提：購入者と商品
        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $product = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト',
            'image_path' => 'dummy.jpg',
            'condition' => 1,
        ]);

        $service = new OrderPaymentMethodService();

        $shipping = [
            'postcode' => '123-4567',
            'address'  => '東京都テスト1-2-3',
            'building' => 'テストビル101',
            'name'     => '購入者',
        ];

        $order = $service->createOrder($buyer, $product, 1, $shipping);

        // コンビニ支払い'1'がDBに入る
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
        ]);
    }

    // Case1 小計画面で変更が反映される ※カード払いを選択した時
    public function test_selected_payment_method_is_card()
    {
        // 前提：購入者と商品
        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $product = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト',
            'image_path' => 'dummy.jpg',
            'condition' => 1,
        ]);

        $service = new OrderPaymentMethodService();

        $shipping = [
            'postcode' => '123-4567',
            'address'  => '東京都テスト1-2-3',
            'building' => 'テストビル101',
            'name'     => '購入者',
        ];

        $order = $service->createOrder($buyer, $product, 2, $shipping);

        // カード払い'2'がDBに入る
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_method' => 2,
            'status' => Order::STATUS_PENDING,
        ]);
    }
}
