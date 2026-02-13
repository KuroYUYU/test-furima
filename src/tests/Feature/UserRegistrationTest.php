<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 1 : 会員登録機能

    // Case1 名前が入力されていない場合、バリデーションメッセージが表示される
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '', // 名前未入力
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // バリデーションで戻る（302）こと
        $response->assertStatus(302);

        $response->assertSessionHasErrors(['name']);

        // メッセージが要件文言どおりであること
        $this->assertSame(
            'お名前を入力してください',
            session('errors')->first('name')
        );
    }

    // Case2 メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト　太郎',
            'email' => '', // メールアドレス未入力
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['email']);

        $this->assertSame(
            'メールアドレスを入力してください',
            session('errors')->first('email')
        );
    }

    // Case3 パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => '', // パスワード未入力
            'password_confirmation' => '',
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードを入力してください',
            session('errors')->first('password')
        );
    }

    // Case4 パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => 'pass123', // パスワード7文字以下
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードは8文字以上で入力してください',
            session('errors')->first('password')
        );
    }

    // Case5 パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password124', // パスワードが不一致
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['password']);

        $this->assertSame(
            'パスワードと一致しません',
            session('errors')->first('password')
        );
    }

    // Case6 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
    public function test_register_success_creates_user_and_redirects_to_profile()
    {
        $response = $this->post('/register', [
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 期待：初回遷移はプロフィール設定画面へ
        $response->assertRedirect(route('mypage.profile.edit'));

        // 期待：DBに登録したユーザーが入る
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name'  => 'テスト 太郎',
        ]);
    }
}
