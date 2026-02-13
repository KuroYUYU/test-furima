<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class MyPageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // このファイルではマイページに関わるテストケース一覧の項目ID 13,14をまとめている

    // 13 : ユーザー情報取得

    // Case1 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
    public function test_mypage_index_check()
    {
        // 出品者
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        // 購入用の商品を作成
        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        // マイページで確認するユーザー
        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // プロフィールを作成
        Profile::create([
            'user_id' => $user->id,
            'profile_image_path' => 'profile.png',
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        // 自分の出品で生表示させる商品を作る
        $p2 = Product::create([
            'user_id' => $user->id,
            'name' => 'テスト商品B',
            'price' => 2000,
            'description' => 'テストB',
            'image_path' => 'dummy2.png',
            'condition' => 1,
        ]);

        // 表示させるための商品を購入
        Order::create([
            'user_id' => $user->id,
            'product_id' => $p1->id,
            'price' => 1000,
            'shipping_postcode' => '123-4567',
            'shipping_address' => '神奈川県テスト1-1',
            'shipping_building' => 'テストビル',
            'shipping_name' => '確認ユーザー',
            'payment_method' => 1,
            'status' => Order::STATUS_PAID,
        ]);

        // マイページを開く
        $res = $this->get(route('mypage.index'));
        $res->assertStatus(200);

        // ニックネームとプロフィール画像が表示されている
        $res->assertSee('テストくん');
        $res->assertSee('profile.png', false);

        // 出品した商品タブで自分の出品が表示されている
        $resSell = $this->get(route('mypage.index', ['page' => 'sell']));
        $resSell->assertStatus(200);
        $resSell->assertSee('テスト商品B');
        $resSell->assertSee('dummy2.png', false);

        // 購入した商品タブで購入した商品が表示されている
        $resBuy = $this->get(route('mypage.index', ['page' => 'buy']));
        $resBuy->assertStatus(200);
        $resBuy->assertSee('テスト商品A');
        $resBuy->assertSee('dummy1.png', false);
        $resBuy->assertSee('Sold');
    }

    // 14 : ユーザー情報変更

    // Case1 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
    public function test_profile_default_values_check()
    {
        // マイページで確認するユーザー
        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // プロフィールを作成
        Profile::create([
            'user_id' => $user->id,
            'profile_image_path' => 'profile.png',
            'nickname' => 'テストくん',
            'postcode' => '123-4567',
            'address' => '神奈川県テスト1-1',
            'building' => 'テストビル',
        ]);

        $res = $this->get(route('mypage.index'));
        $res->assertStatus(200);

        // プロフィール編集画面を開く
        $res = $this->get(route('mypage.profile'));
        $res->assertStatus(200);

        // プロフィール編集画面で登録した初期値が表示されている
        $res->assertSee('テストくん');
        $res->assertSee('profile.png', false);
        $res->assertSee('123-4567');
        $res->assertSee('神奈川県テスト1-1');
        $res->assertSee('テストビル');
    }
}
