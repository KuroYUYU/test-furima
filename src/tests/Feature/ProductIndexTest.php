<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class ProductIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // このファイルでは商品一覧画面に関わるテストケース一覧の項目ID 4,5,6をまとめている
    // 4 : 商品一覧取得

    // Case1 全商品を取得できる ※未ログインユーザーで確認
    public function test_guest_can_view_all_products_index()
    {
        // 出品者
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        // 商品を2つ作成
        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $p2 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品B',
            'price' => 2000,
            'description' => 'テストB',
            'image_path' => 'dummy2.png',
            'condition' => 1,
        ]);

        // 未ログインで一覧取得
        $this->assertGuest();
        $res = $this->get(route('index'));
        $res->assertStatus(200);

        // 一覧で商品名と画像イメージの表示を確認できる
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);
        $res->assertSee($p2->name);
        $res->assertSee('dummy2.png', false);
    }

    // Case2 購入済み商品は「Sold」と表示される
    public function test_sold_check_index()
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

        // 「購入済み（PAID）」の注文データを作成
        Order::create([
            'user_id' => $buyer->id,
            'product_id' => $p1->id,
            'price' => $p1->price,
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
            'stripe_checkout_session_id' => 'dummy-session',
        ]);

        // 一覧取得
        $res = $this->get(route('index'));
        $res->assertStatus(200);

        // 表示された商品にSoldが表示される
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);
        $res->assertSee('Sold');
    }

    // Case3 自分が出品した商品は表示されない
    public function test_my_products_not_show_index()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $this->actingAs($seller);

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => '自分の商品',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        // 別のユーザーを作成
        $user = User::create([
            'name' => '他ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $p2 = Product::create([
            'user_id' => $user->id,
            'name' => '他人の商品',
            'price' => 2000,
            'description' => 'テストB',
            'image_path' => 'dummy2.png',
            'condition' => 1,
        ]);

        $res = $this->get(route('index'));
        $res->assertStatus(200);

        // 自分が出品した商品は一覧に表示されない
        $res->assertDontSee($p1->name);
        $res->assertDontSee('dummy1.png', false);

        // 他人の商品は表示されることも確認
        $res->assertSee($p2->name);
        $res->assertSee('dummy2.png', false);
    }

    // 5 : マイリスト一覧取得

    // Case1 いいねした商品だけが表示される
    public function test_like_only_index()
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

        $p2 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品B',
            'price' => 1000,
            'description' => 'テストB',
            'image_path' => 'dummy2.png',
            'condition' => 1,
        ]);

        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // p1の商品のいいねを実行
        $this->post(route('likes.store', $p1));

        // マイリストタブの一覧表示
        $res = $this->get('/?tab=mylist');
        $res->assertStatus(200);

        // いいねした商品(p1)は表示される
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);

        // いいねしていない商品(p2)は表示されない
        $res->assertDontSee($p2->name);
        $res->assertDontSee('dummy2.png', false);
    }

    // Case2 購入済み商品は「Sold」と表示される
    public function test_mylist_tab_sold_check_index()
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


        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // p1の商品のいいねを実行
        $this->post(route('likes.store', $p1));

        // 「購入済み（PAID）」の注文データを作成
        Order::create([
            'user_id' => $user->id,
            'product_id' => $p1->id,
            'price' => $p1->price,
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '購入者',
            'stripe_checkout_session_id' => 'dummy-session',
        ]);

        // マイリストタブの一覧表示
        $res = $this->get('/?tab=mylist');
        $res->assertStatus(200);

        // いいねした商品(p1)は表示される
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);
        $res->assertSee('Sold');
    }

    // Case3 未認証の場合は何も表示されない
    public function test_guest_cannot_view_mylist_tab_index()
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

        // 未ログインでマイリストを取得
        $this->assertGuest();
        $res = $this->get('/?tab=mylist');
        $res->assertStatus(200);

        // マイリストに何も表示されない
        $res->assertDontSee($p1->name);
        $res->assertDontSee('dummy1.png', false);
    }

    // 6 : 商品検索機能

    // Case1 「商品名」で部分一致検索ができる
    public function test_product_name_search()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => '検索用商品',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $p2 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品B',
            'price' => 2000,
            'description' => 'テストB',
            'image_path' => 'dummy2.png',
            'condition' => 1,
        ]);

        // 検索対象の一覧を取得（p1を部分一致検索している）
        $res = $this->get('/?keyword=検索');
        $res->assertStatus(200);

        // 部分一致検索した商品が表示される
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);

        // 検索対象外の商品は表示されない
        $res->assertDontSee($p2->name);
        $res->assertDontSee('dummy2.png', false);
    }

    // Case2 検索状態がマイリストでも保持されている
    public function test_product_name_search_keep_mylist()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => '検索用商品',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        $p2 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品B',
            'price' => 2000,
            'description' => 'テストB',
            'image_path' => 'dummy2.png',
            'condition' => 1,
        ]);

        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // p1,p2の商品のいいねを実行
        $this->post(route('likes.store', $p1));
        $this->post(route('likes.store', $p2));

        // 検索対象の一覧を取得（p1を部分一致検索している）
        $res = $this->get('/?keyword=検索');
        $res->assertStatus(200);

        // マイリストリンクにkeywordが保持されていることを確認
        $res->assertSee('tab=mylist&keyword=検索', false);

        // 検索した状態でマイリストに切り替える
        $res = $this->get('/?tab=mylist&keyword=検索');
        $res->assertStatus(200);

        // マイリストで部分一致検索した商品が表示される
        $res->assertSee($p1->name);
        $res->assertSee('dummy1.png', false);

        // いいねしていても検索対象外の商品は表示されない
        $res->assertDontSee($p2->name);
        $res->assertDontSee('dummy2.png', false);
    }
}
