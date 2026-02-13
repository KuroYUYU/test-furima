# furima

## 環境構築
### Dockerビルド
1. `git clone git@github.com:KuroYUYU/test-mogitate.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`
### Laravel環境構築
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、`cp.env.example.env`で新しく.envファイルを作成
4. .envに以下の環境変数を追加
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成

`php artisan key:generate`

6. マイグレーションの実行

`php artisan migrate`

7. シーディングの実行

`php artisan db:seed`

8. シンボリックリンク作成

`php artisan storage:link`

## Stripe設定（決済機能を使用する場合に必要）
最下部のURLよりStripに会員登録をしてください。

決済機能を動作させるには、Stripeのテストキーを `.env` に設定してください。

1. Stripeのダッシュボード（テストモード）で以下を取得  
   - 公開可能キー
   - シークレットキー

2. `.env` に設定

`STRIPE_KEY=pk_test_xxxxxxxxx`
`STRIPE_SECRET=sk_test_xxxxxxxxx`

## テスト実行方法と備考
`php artisan test ...`(テスト対象)を入れてコマンドを実行
- テストケース一覧のID NO11のみUnitテストに記載
- その他の項目はFuatureテストに記載
- 今回のテストケースにて疑問点があり確認した部分について記載
1. 【商品一覧取得】Case 全商品を取得できる

`自分が出品した商品は一覧に表示されない仕様のため未ログインユーザーで確認する`

2. 【コメント送信機能】ログイン前のユーザーはコメントを送信できない

`未ログインユーザーがコメントをした場合ミドルウェアでログイン画面に遷移させることでコメントできないものとしています`

## その他今回のアプリ作成での備考
- `機能要件に記載のないバリデーションエラーメッセージは任意の適切な文言で作成`
- `自分の商品を購入できることを阻止するため自分の商品詳細画面では「購入手続きへ」ボタンを非表示にさせています`
- `Soldの商品に関しても再購入不可にするため「購入手続きへ」ボタンを非表示にさせています`
- `メール認証機能はMaiHogを使用しています,.envにてMAIL_FROM_ADDRESSは任意のアドレスを設定してください。`
- `Stripe接続はカード決済のみでコンビニ決済は即購入になっています（コーチに相談済み）`

## 使用技術(実行環境)
- PHP:8.1.33
- Laravel:8.83.29
- MySQL:Ver 8.0.26
- nginx:1.21.1
## テーブル設計
<img width="840" height="612" alt="スクリーンショット 2026-02-13 5 57 57" src="https://github.com/user-attachments/assets/8379e275-3152-4513-877d-9145e44ee996" />
<img width="841" height="520" alt="スクリーンショット 2026-02-13 5 58 23" src="https://github.com/user-attachments/assets/5a3864e4-6f60-4049-9693-dc8a301f6b3d" />
<img width="839" height="296" alt="スクリーンショット 2026-02-13 5 58 41" src="https://github.com/user-attachments/assets/8b26bc57-59fe-4481-8e9b-e57fe62a007b" />

## ER図
<img width="904" height="616" alt="スクリーンショット 2026-02-13 4 38 16" src="https://github.com/user-attachments/assets/02cf73b7-1cd6-4316-b69f-e753e679cb90" />

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
- MaiHog(メール認証用)：http://localhost:8025/
- stripe：https://stripe.com/jp




