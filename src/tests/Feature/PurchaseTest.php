<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // このファイルでは購入に関わるテストケース一覧の項目ID 10,12をまとめている
    // 項目11のみUnitTestの方に記載しています（運営より確認回答済み）
    // また10のcase1については２つ支払い方法のテストでそれぞれテストしている

    // 10 : 商品購入機能

    // Case1（コンビニ払い） 「購入する」ボタンを押下すると購入が完了する
    // ※コンビニ払いはstripeを通さないため即購入完了
    public function test_purchase_success()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $buyer->markEmailAsVerified();

        $this->actingAs($buyer);

        // 購入者のプロフィールを作成
        Profile::create([
            'user_id' => $buyer->id,
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 商品購入画面を開く
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        $res = $this->post(route('purchase.store', $p1), [
            'payment_method' => 1, // コンビニ払い
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'product_id' => $p1->id,
            'price' => 1000,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
        ]);
    }

    // Case1（カード払い） 「購入する」ボタンを押下すると購入が完了する
    // ※カード払いはstripeに接続されSTATUS_PENDINGまでの流れを確認
    public function test_purchase_success2()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $buyer->markEmailAsVerified();

        $this->actingAs($buyer);

        // 購入者のプロフィールを作成
        Profile::create([
            'user_id' => $buyer->id,
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 商品購入画面を開く
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        $res = $this->post(route('purchase.store', $p1), [
            'payment_method' => 2, // カード払い
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
        ]);

        $res->assertStatus(302);

        // カード払いはSTATUS_PENDINGの状態になる部分までの処理を見て完了としています
        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'product_id' => $p1->id,
            'price' => 1000,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
            'payment_method' => 2,
            'status' => Order::STATUS_PENDING,
        ]);

        // 購入押下後にstripeに接続されstripe_checkout_session_idが発行される
        $order = Order::first();
        $this->assertNotNull($order->stripe_checkout_session_id);
    }

    // Case2 購入した商品は商品一覧画面にて「sold」と表示される
    public function test_purchase_success_sold_index()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $buyer->markEmailAsVerified();

        $this->actingAs($buyer);

        Profile::create([
            'user_id' => $buyer->id,
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 商品購入画面を開く
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        $res = $this->post(route('purchase.store', $p1), [
            'payment_method' => 1, // コンビニ払い
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'product_id' => $p1->id,
            'price' => 1000,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
        ]);

        // 商品一覧画面を取得
        $res = $this->get(route('index'));
        $res->assertStatus(200);

        // 表示された商品にSoldが表示される
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);
        $res->assertSee('Sold');
    }

    // Case3 「プロフィール/購入した商品一覧」に追加されている
    public function test_purchased_product_show_my_page()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $buyer->markEmailAsVerified();

        $this->actingAs($buyer);

        Profile::create([
            'user_id' => $buyer->id,
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 商品購入画面を開く
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        $res = $this->post(route('purchase.store', $p1), [
            'payment_method' => 1, // コンビニ払い
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'product_id' => $p1->id,
            'price' => 1000,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
        ]);

        // マイページの「購入した商品」タブを取得
        $res = $this->get(route('mypage.index', ['page' => 'buy']));
        $res->assertStatus(200);

        // 購入商品が表示されSoldが出ている
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);
        $res->assertSee('Sold');
    }

    // 12 : 配送先変更機能

    // Case1 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_purchase_address_chang()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $buyer->markEmailAsVerified();

        $this->actingAs($buyer);

        // プロフィールのデフォルト住所
        Profile::create([
            'user_id' => $buyer->id,
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 商品購入画面を開く
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        // 住所変更画面を開く
        $res = $this->get(route('purchase.address.edit', $p1));
        $res->assertStatus(200);

        $res = $this->post(route('purchase.address.store', $p1), [
            'postcode' => '111-9999',
            'address' => '東京都テスト9-9',
            'building' => '変更ビル',
        ]);

        $res->assertStatus(302);

        // セッションに入ったことを確認
        $this->assertEquals('111-9999', session('purchase.shipping.postcode'));
        $this->assertEquals('東京都テスト9-9', session('purchase.shipping.address'));
        $this->assertEquals('変更ビル', session('purchase.shipping.building'));

        // 購入画面で反映確認
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        $res->assertSee('111-9999');
        $res->assertSee('東京都テスト9-9');
        $res->assertSee('変更ビル');
    }

    // Case2 購入した商品に送付先住所が紐づいて登録される
    public function test_purchase_address_chang2()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $buyer->markEmailAsVerified();

        $this->actingAs($buyer);

        // プロフィールのデフォルト住所
        Profile::create([
            'user_id' => $buyer->id,
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 商品購入画面を開く
        $res = $this->get(route('purchase.create', $p1));
        $res->assertStatus(200);

        // 住所変更画面を開く
        $res = $this->get(route('purchase.address.edit', $p1));
        $res->assertStatus(200);

        $res = $this->post(route('purchase.address.store', $p1), [
            'postcode' => '111-9999',
            'address' => '東京都テスト9-9',
            'building' => '変更ビル',
        ]);

        $res->assertStatus(302);

        // セッションに入ったことを確認
        $this->assertEquals('111-9999', session('purchase.shipping.postcode'));
        $this->assertEquals('東京都テスト9-9', session('purchase.shipping.address'));
        $this->assertEquals('変更ビル', session('purchase.shipping.building'));

        // 住所変更された状態で購入する
        $res = $this->post(route('purchase.store', $p1), [
            'payment_method' => 1, // コンビニ払い
            'shipping_postcode' => '111-9999',
            'shipping_address' => '東京都テスト9-9',
            'shipping_building' => '変更ビル',
            'shipping_name' => '購入者',
        ]);

        $res->assertStatus(302);

        // 変更した情報でDBに反映される
        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'product_id' => $p1->id,
            'price' => 1000,
            'shipping_postcode' => '111-9999',
            'shipping_address' => '東京都テスト9-9',
            'shipping_building' => '変更ビル',
            'shipping_name' => '購入者',
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
        ]);
    }
}
