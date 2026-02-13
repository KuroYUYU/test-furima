<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

class EmailVerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    // 16 : メール認証機能

    // Case1 会員登録後、認証メールが送信される
    public function test_register_send_verification_email()
    {
        // メール通知をした程にさせる
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テスト　太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);

        // DBにユーザーが登録される
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        // 認証メールが送られたを確認
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    // Case2 メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
    // ※MailHogは開発用のメール確認ツールのため、Featureテストではリンクの表示を検証しました
    public function test_click_verify_button_mailhog()
    {
        // メール通知をした程にさせる
        Notification::fake();

        // 未認証ユーザー
        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        // 認証導線画面を表示
        $res = $this->get('/email/verify');
        $res->assertStatus(200);

        // リンクがあること（hrefの一致で今回は遷移できる程としています）
        // 認証サイトに遷移(私の実装はMaiHog)はブラウザE2EテストのためFeatureテストでの確認不可
        $res->assertSee('http://localhost:8025', false);
    }


    // Case3 メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する
    public function test_email_verification_after_profile_edit()
    {
        // 未認証ユーザー
        $user = User::create([
            'name' => '確認ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        // メールに入る「認証リンク」をテスト内で生成
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user);

        // 認証リンクを踏む → after-verify へ
        $res = $this->get($verifyUrl);
        $res->assertRedirect(route('profile.after.verify'));

        // 認証済みになっていること
        $this->assertNotNull($user->fresh()->email_verified_at);

        // after-verify に行く → 初回登録時はプロフィール編集画面へ遷移
        $res2 = $this->get(route('profile.after.verify'));
        $res2->assertRedirect(route('mypage.profile.edit'));
    }
}
