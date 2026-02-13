<?php

namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;



class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // メール認証が未認証なら誘導画面へ
            if (! $request->user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // 認証済みなら通常通り
            return redirect()->intended('/');
        }

        // 失敗時：エラーメッセージを返す  auth.phpのfailedを使う
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}
