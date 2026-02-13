<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class ProductDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 7 : 商品詳細情報取得

    // Case1 必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
    public function test_can_view_product_all_detail()
    {
        // 出品者
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        // 前提条件の商品を作成
        $p1 = Product::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品A',
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        // 単数カテゴリを設定
        $category = Category::create([
            'name' => 'ファッション',
        ]);
        $p1->categories()->attach($category->id);

        // 確認者
        $user = User::create([
            'name' => '確認者',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        // 前提条件の商品にいいねをつける
        $this->post(route('likes.store', $p1));

        // 前提条件の商品にいいねをつけるコメントをする
        $this->post(route('comments.store', $p1), [
            'body' => 'テスト本文',
        ]);

        // 商品の詳細を取得
        $res = $this->get(route('products.detail', $p1));
        $res->assertStatus(200);

        // 詳細で表示される内容
        $res->assertSee($p1->name);
        $res->assertSee($p1->brand_name);
        $res->assertSee('良好'); // conditionはビューに表示される文字を記載しました
        $res->assertSee('dummy1.png', false);
        $res->assertSee('¥');
        $res->assertSee(number_format($p1->price));
        $res->assertSee($category->name);
        $res->assertSee($p1->description);

        // コメント（ユーザー名＆本文）
        $res->assertSee('確認者');
        $res->assertSee('テスト本文');

        // いいね・コメントの数値
        $res->assertSee('<span class="detail__count js-like-count">1</span>', false);
        $res->assertSee('コメント(1)');
    }

    // Case2 複数選択されたカテゴリが表示されているか
    public function test_can_view_product_many_category()
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
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'description' => 'テストA',
            'image_path' => 'dummy1.png',
            'condition' => 1,
        ]);

        // カテゴリを複数作成
        $cat1 = Category::create(['name' => 'ファッション']);
        $cat2 = Category::create(['name' => 'メンズ']);
        $cat3 = Category::create(['name' => 'スポーツ']);
        $cat4 = Category::create(['name' => 'アクセサリ']);

        // 複数のカテゴリをattach
        $p1->categories()->attach([$cat1->id, $cat2->id, $cat3->id, $cat4->id]);

        // 確認者
        $user = User::create([
            'name' => '確認者',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        $res = $this->get(route('products.detail', $p1));
        $res->assertStatus(200);

        // 複数作成したカテゴリが全て表示される
        $res->assertSee($cat1->name);
        $res->assertSee($cat2->name);
        $res->assertSee($cat3->name);
        $res->assertSee($cat4->name);
    }
}
