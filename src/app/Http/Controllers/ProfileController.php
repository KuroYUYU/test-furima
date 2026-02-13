<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->load('profile');

        // 登録後初回遷移時はトップ
        $redirectTo = '/';

        // マイページ→プロフィールの場合はマイページへ
        if ($request->query('from') === 'index') {
            $redirectTo = route('mypage.index');
        }

        return view('mypage.profile', compact('user', 'redirectTo'));
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        $profileData = $request->only(['nickname', 'postcode', 'address', 'building']);

        // 画像（任意）
        if ($request->hasFile('profile_image_path')) {
            $path = $request->file('profile_image_path')->store('profiles', 'public');
            $profileData['profile_image_path'] = $path;
        }

        // profilesがなければ作る、あれば更新
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // 更新後の遷移先切り替え
        return redirect($request->input('redirect_to', '/'));
    }
}
