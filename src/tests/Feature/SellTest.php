<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;

class SellTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 15 : 出品商品情報登録

    // Case1 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
    public function test_sell_success()
    {
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        $seller->markEmailAsVerified();

        $this->actingAs($seller);

        $res = $this->get(route('products.store'));
        $res->assertStatus(200);

        // カテゴリを定義
        $cat1 = Category::create(['name' => 'ファッション']);
        $cat2 = Category::create(['name' => 'メンズ']);

        // 登録用のダミーファイルを用意
        $file = UploadedFile::fake()->create(
            'dummy.png',10,'image/png'
        );

        // 出品商品を作成
        $product = [
            'image' => $file,
            'name' => 'テスト商品A',
            'brand_name' => 'テスト工業',
            'price' => 1000,
            'description' => 'テストA',
            'condition' => 1,
            'category_ids' => [$cat1->id, $cat2->id],
        ];

        // 出品を保存する
        $res = $this->post(route('products.store'), $product);

        // 念の為POSTがバリデーションで弾かれてないことを保証
        $res->assertSessionHasNoErrors();

        $res->assertStatus(302);

        // 1件出品ができる
        $this->assertDatabaseCount('products', 1);

        $this->assertDatabaseHas('products', [
            'user_id'     => $seller->id,
            'name'        => 'テスト商品A',
            'brand_name'  => 'テスト工業',
            'price'       => 1000,
            'description' => 'テストA',
            'condition'   => 1,
        ]);

        // 最新の1件の出品を取得
        $productModel = Product::latest('id')->first();

        // 画像は保存時に処理でファイル名が変わるため名前でなく存在を確認
        $this->assertNotNull($productModel->image_path);

        // カテゴリが商品に紐づいているかの確認
        $this->assertDatabaseHas('product_category', [
            'product_id' => $productModel->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('product_category', [
            'product_id' => $productModel->id,
            'category_id' => $cat2->id,
        ]);
    }
}
