<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseAddressController;
use App\Http\Controllers\MyPageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ログインの際LoginRequestでバリデーションを使用するため設定
Route::post('/login', [LoginController::class, 'store'])->name('login');

// プロフィール編集
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->middleware(['auth', 'verified'])
    ->name('mypage.profile.edit');
Route::post('/mypage/profile', [ProfileController::class, 'update'])->middleware(['auth', 'verified'])
    ->name('mypage.profile.update');

// 商品の一覧・出品・詳細
Route::get('/', [ProductController::class, 'index'])->name('index');
Route::get('/sell', [ProductController::class, 'create'])->middleware(['auth', 'verified'])
    ->name('products.sell');
Route::post('/sell', [ProductController::class, 'store'])->middleware(['auth', 'verified'])
    ->name('products.store');
Route::get('/item/{product}', [ProductController::class, 'show'])->name('products.detail');

// コメント投稿
Route::post('/products/{product}/comments', [CommentController::class, 'store'])->middleware(['auth', 'verified'])
    ->name('comments.store');

// 「いいね」増減
Route::post('/products/{product}/likes', [LikeController::class, 'store'])->middleware(['auth', 'verified'])
    ->name('likes.store');
Route::delete('/products/{product}/likes', [LikeController::class, 'destroy'])->middleware(['auth', 'verified'])
    ->name('likes.destroy');

// 購入確定まで 決済成功ルート
// モデルバインディング防止のためsuccess,cancelを上に記述
Route::get('/purchase/success', [PurchaseController::class, 'success'])->middleware(['auth', 'verified'])
    ->name('purchase.success');
Route::get('/purchase/{product}', [PurchaseController::class, 'create'])->middleware(['auth', 'verified'])
    ->name('purchase.create');
Route::post('/purchase/{product}', [PurchaseController::class, 'store'])->middleware(['auth', 'verified'])
    ->name('purchase.store');

// 購入前住所変更
Route::get('/purchase/{product}/address', [PurchaseAddressController::class, 'edit'])->middleware(['auth', 'verified'])
    ->name('purchase.address.edit');

// 住所変更を一時保存（セッション）のためstoreを命名
Route::post('/purchase/{product}/address', [PurchaseAddressController::class, 'store'])->middleware(['auth', 'verified'])
    ->name('purchase.address.store');

// マイページ
Route::get('/mypage', [MyPageController::class, 'index'])->middleware(['auth', 'verified'])
    ->name('mypage.index');
Route::get('/profile', [ProfileController::class, 'edit'])->middleware(['auth', 'verified'])
    ->name('mypage.profile');

//  メール認証ルート
// 認証誘導画面（notice）
Route::get('/email/verify', function () {return view('auth.verify-email');})->middleware('auth')
    ->name('verification.notice');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {$request->user()
    ->sendEmailVerificationNotification();return back();
})->middleware(['auth','throttle:6,1'])->name('verification.send');

// メールのリンクを踏んだ時（認証完了）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();return redirect()->route('profile.after.verify');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/after-verify', function () {
    $user = auth()->user();

    // 初回 プロフィール登録画面を表示
    if ($user->profile === null) {
        return redirect()->route('mypage.profile.edit');
    }

    // 既に登録済みならトップ
    return redirect('/');
})->middleware('auth')->name('profile.after.verify');