<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class LikeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 8 : いいね機能

    // Case1 いいねアイコンを押下することによって、いいねした商品として登録することができる。
    public function test_user_can_like_product()
    {
        // 商品を出品するユーザーを作成
        $seller = User::create([
            'name' => 'テスト　一郎',
            'email' => 'test0@example.com',
            'password' => Hash::make('password123'),
        ]);

        // ユーザー作成後にメール認証
        $seller->markEmailAsVerified();

        // 商品を作成
        $product = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト',
            'image_path' => 'dummy.png',
            'condition' => 1,
        ]);

        // 商品をいいねするユーザー一人目を作成
        $user1 = User::create([
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user1->markEmailAsVerified();

        $this->actingAs($user1);

        // 商品詳細ページを開く
        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);
        $res->assertSee('js-like-btn', false); // いいねボタンがあることを確認

        // いいね前は0
        $this->assertDatabaseCount('likes', 0);

        // 一人目がいいねを実行
        $this->post(route('likes.store', $product));

        $this->assertDatabaseHas('likes', [
            'user_id' => $user1->id,
            'product_id' => $product->id,
        ]);

        // いいね数が１に増える
        $this->assertDatabaseCount('likes', 1);

        // 商品をいいねするユーザー二人目を作成
        $user2 = User::create([
            'name' => 'テスト　二郎',
            'email' => 'test2@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user2->markEmailAsVerified();

        $this->actingAs($user2);

        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);
        $res->assertSee('js-like-btn', false);

        // 二人目がいいねを実行
        $this->post(route('likes.store', $product));

        $this->assertDatabaseHas('likes', [
            'user_id' => $user2->id,
            'product_id' => $product->id,
        ]);

        // いいね数が2に増える
        $this->assertDatabaseCount('likes', 2);
    }

    // Case2 追加済みのアイコンは色が変化する
    public function test_like_icon_color_changes()
    {
        // 商品を出品するユーザーを作成
        $seller = User::create([
            'name' => 'テスト　一郎',
            'email' => 'test0@example.com',
            'password' => Hash::make('password123'),
        ]);

        // ユーザー作成後にメール認証
        $seller->markEmailAsVerified();

        // 商品を作成
        $product = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト',
            'image_path' => 'dummy.png',
            'condition' => 1,
        ]);

        // 商品をいいねするユーザーを作成
        $user = User::create([
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // いいね前 詳細のルートへ
        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);

        // いいねのされていないハートの色を変える
        $res->assertSee('js-like-btn', false);
        $res->assertDontSee('is-liked', false);

        // ここでいいねされDBに登録
        $this->post(route('likes.store', $product));

        // いいね後 色が変化
        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);

        $res->assertSee('is-liked', false);
    }

    // Case3 再度いいねアイコンを押下することによって、いいねを解除することができる。
    public function test_user_can_unlike_product()
    {
        // 商品を出品するユーザーを作成
        $seller = User::create([
            'name' => 'テスト　一郎',
            'email' => 'test0@example.com',
            'password' => Hash::make('password123'),
        ]);

        // ユーザー作成後にメール認証
        $seller->markEmailAsVerified();

        // 商品を作成
        $product = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト',
            'image_path' => 'dummy.png',
            'condition' => 1,
        ]);

        // 商品をいいねするユーザーを作成
        $user = User::create([
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // 商品詳細ページを開く
        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);
        $res->assertSee('js-like-btn', false); // いいねボタンがあることを確認

        // いいね前は0
        $this->assertDatabaseCount('likes', 0);

        // いいねを実行
        $this->post(route('likes.store', $product));

        // DBにいいねが登録される
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // いいね数が１に増える
        $this->assertDatabaseCount('likes', 1);

        // 再度いいねを実行(destroyを叩く)
        $this->delete(route('likes.destroy', $product));

        // いいね数が0に減る
        $this->assertDatabaseCount('likes', 0);

        // DBのいいねが消える
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
