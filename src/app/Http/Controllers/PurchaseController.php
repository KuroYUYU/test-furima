<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use App\Models\Product;
use App\Models\Order;

class PurchaseController extends Controller
{
    public function create(Product $product)
    {
        $paymentMethods = [
            1 => 'コンビニ払い',
            2 => 'カード支払い',
        ];

        $profile = auth()->user()->profile;
        $temp = session('purchase.shipping');

        // セッションが残っている場合別の商品を開いたら破棄する
        if ($temp && ($temp['product_id'] ?? null) !== $product->id) {
            session()->forget('purchase.shipping');
            $temp = null;
        }

        // 表示の際セッションを優先、なければプロフィールを取得し表示
        $postcode = data_get($temp, 'postcode')  ?? $profile->postcode;
        $address  = data_get($temp, 'address')   ?? $profile->address;
        // 変更の際、空欄でも更新されるように
        $building = array_key_exists('building', $temp ?? []) ? ($temp['building'] ?? null) : $profile->building;

        $shippingAddress = [
            'postcode' => $postcode,
            'text' => trim($address . ' ' . $building),
        ];

        return view('purchase.purchase', compact('product', 'paymentMethods', 'shippingAddress'));
    }

    public function store(PurchaseRequest $request, Product $product)
    {
        $user = $request->user();
        // デフォルトの配送先はprofileから
        $profile = $user->profile;

        $temp = session('purchase.shipping');

        // セッションがあれば優先、なければプロフィールでcreate
        $postcode = data_get($temp, 'postcode')  ?? $profile->postcode;
        $address  = data_get($temp, 'address')   ?? $profile->address;
        $building = array_key_exists('building', $temp ?? []) ? ($temp['building'] ?? null) : $profile->building;

        $order = Order::create([
            'user_id'                     => $user->id,
            'product_id'                  => $product->id,
            'shipping_name'               => $user->name,
            'price'                       => $product->price,
            'payment_method'              => $request->payment_method,
            'shipping_postcode'           => $postcode,
            'shipping_address'            => $address,
            'shipping_building'           => $building,
            'status'                      => Order::STATUS_PENDING, // モデルから取る
            'stripe_checkout_session_id'  => null,  // Stripe session作成後に入れる
        ]);

        // セッションで住所変更したものは削除する
        session()->forget('purchase.shipping');

        // コンビニ払いはStripeに接続せず即購入完了
        if ((int) $request->payment_method === 1) {
            $order->update([
                'status' => Order::STATUS_PAID,
            ]);

            return redirect('/');
        }

        // ここから決済（stripe）
        Stripe::setApiKey(config('services.stripe.secret'));

        // 今回はカードのみ
        $paymentMethodTypes = ['card'];

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => $paymentMethodTypes,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int) $order->price,
                    'product_data' => [
                        'name' => $product->name ?? '商品',
                    ],
                ],
            ]],
            'client_reference_id' => (string) $order->id,
            'metadata' => [
                'order_id' => (string) $order->id,
                'product_id' => (string) $product->id,
                'user_id' => (string) $user->id,
            ],
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            // キャンセル専用のルートは今回未作成です
            'cancel_url'  => url('/'),
        ]);

        $order->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect('/');
        }

        // Stripe側でセッションが存在するか確認
        Stripe::setApiKey(config('services.stripe.secret'));
        CheckoutSession::retrieve($sessionId);

        // 自分のDBで該当注文を探す
        $order = Order::where('stripe_checkout_session_id', $sessionId)->first();

        // もし注文データが見つからなかったら、処理を打ち切って安全に戻す(異常時の保険として記載)
        if (!$order) {
            return redirect('/')->with('message', '注文が見つかりませんでした');
        }

        $order->update([
            'status' => Order::STATUS_PAID,
        ]);

        return redirect('/');
    }
}
