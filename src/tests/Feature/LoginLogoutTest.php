<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginLogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // このファイルではログイン,ログアウトに関わるテストケース一覧の項目ID 1,2をまとめている
    // 2 : ログイン機能

    // Case1 メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '', // メールアドレス未入力
            'password' => 'password123',
        ]);

        // バリデーションで戻る
        $response->assertStatus(302);

        $response->assertSessionHasErrors(['email']);

        // メッセージが要件文言どおりであること
        $this->assertSame(
            'メールアドレスを入力してください',
            session('errors')->first('email')
        );
    }

    // Case2 パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '', // パスワード未入力
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードを入力してください',
            session('errors')->first('password')
        );
    }

    // Case3 入力情報が間違っている場合、バリデーションメッセージが表示される
    public function test_login_failed_with_wrong_password()
    {
        // 事前にユーザー作成し正しいパスワードで保存
        User::create([
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 間違ったパスワードでログイン
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password999',
        ]);

        $response->assertStatus(302);

        // emailの下部にエラー表示
        $response->assertSessionHasErrors(['email']);

        $this->assertSame(
            'ログイン情報が登録されていません',
            session('errors')->first('email')
        );
    }

    // Case4 正しい情報が入力された場合、ログイン処理が実行される
    public function test_login_success()
    {
        // ユーザーを作成
        $user = User::create([
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // メール認証を実装しているためユーザー作成後認証
        $user->markEmailAsVerified();

        // 正しいパスワードでログイン
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // ログイン後はトップを表示
        $response->assertRedirect('/');

        // 正常にログインされる
        $this->assertAuthenticated();
    }

    // 3 : ログアウト機能

    // Case1 ログアウトができる
    public function test_user_can_logout()
    {
        $user = User::create([
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // ユーザー作成後にログイン状態にする
        $this->actingAs($user);

        // ログアウト実行
        $response = $this->post('/logout');

        // ログアウト後ログイン画面に戻る
        $response->assertRedirect(route('login'));

        // 未ログイン状態になっている
        $this->assertGuest();
    }
}
