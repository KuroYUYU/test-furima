<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 9 : コメント送信機能

    // Case1 ログイン済みのユーザーはコメントを送信できる
    public function test_auth_user_can_post_comment()
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

        // コメントするユーザーを作成
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

        // コメント前（0件）
        $this->assertDatabaseCount('comments', 0);

        // コメントを送信
        $commentBody = 'テストコメントです';
        $this->post(route('comments.store', $product), [
            'body' => $commentBody,
        ]);

        // DBに反映される
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'body' => $commentBody,
        ]);

        // コメント後（1件）
        $this->assertDatabaseCount('comments', 1);

        // もう一度詳細を開いてコメントが表示されコメント数の増加も確認
        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);
        $res->assertSee($commentBody);
        $res->assertSee('<span class="detail__count">1</span>', false);
    }

    // Case2 ログイン前のユーザーはコメントを送信できない
    public function test_not_auth_user_cant_post_comment()
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

        // 未ログインユーザー
        $this->assertGuest();

        // 商品詳細ページを開く
        $res = $this->get(route('products.detail', $product));
        $res->assertStatus(200);

        // コメントを送信
        $commentBody = 'テストコメントです';
        // ポストを変数に受け取って保持
        $postRes = $this->post(route('comments.store', $product), [
            'body' => $commentBody,
        ]);

        // ミドルウェアでログイン画面に戻る
        $postRes->assertRedirect();
        $postRes->assertRedirectContains('/login');

        // DBにコメントが増えてない（投稿できてないを保証）
        $this->assertDatabaseCount('comments', 0);
        $this->assertDatabaseMissing('comments', [
            'product_id' => $product->id,
            'body' => $commentBody,
        ]);
    }

    // Case3 コメントが入力されていない場合、バリデーションメッセージが表示される
    public function test_comment_body_is_required()
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

        // コメントするユーザーを作成
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

        // コメントを空で送信
        $response = $this->post(route('comments.store', $product), [
            'body' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'body' => 'コメントを入力してください',
        ]);
    }

    // Case4 コメントが255字以上の場合、バリデーションメッセージが表示される
    // ※テストケース一覧のタイトルは上記だが、正確には256文字以上の場合にバリデーションが表示
    public function test_comment_body_is_max()
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

        // コメントするユーザーを作成
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

        // コメントをmax255文字で送信できる境界線確認を念の為記載
        $this->post(route('comments.store', $product), [
            'body' => str_repeat('あ', 255), // あ を255文字
        ])->assertSessionDoesntHaveErrors(['body']);

        // コメントを256文字で送信
        $body = Str::repeat('あ', 256); // あ を256文字
        $response = $this->post(route('comments.store', $product), [
            'body' => $body,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'body' => 'コメントは255文字以内で入力してください',
        ]);
    }
}
