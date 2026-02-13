@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile">
    <div class="profile__header">
        <h1 class="profile__title">プロフィール設定</h1>
    </div>

    <div class="profile__card">
        <form action="{{ route('mypage.profile.update') }}" class="profile__form" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

            {{-- プロフィール画像 --}}
            <div class="profile__group">
                <div class="profile__image-row">
                    <div class="profile__avatar">
                        {{-- 画像表示（未設定ならダミー） --}}
                        @if(!empty($user->profile->profile_image_path))
                            <img class="profile__avatar-img" src="{{ asset('storage/' . $user->profile->profile_image_path) }}" alt="プロフィール画像">
                        @else
                            <div class="profile__avatar-placeholder"></div>
                        @endif
                    </div>

                    <label class="profile__image-button" for="profile_image_path">
                        画像を選択する
                    </label>
                    <input class="profile__image-input" id="profile_image_path" type="file" name="profile_image_path" accept="image/*">
                </div>

                @error('profile_image_path')
                    <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="profile__group">
                <label class="profile__label" for="nickname">
                    ユーザー名
                </label>
                <input class="profile__input" id="nickname" type="text" name="nickname" value="{{ old('nickname', $user->profile->nickname ?? '') }}">
                @error('nickname')
                    <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 郵便番号 --}}
            <div class="profile__group">
                <label class="profile__label" for="postcode">
                    郵便番号
                </label>
                <input class="profile__input" id="postcode" type="text" name="postcode" value="{{ old('postcode', $user->profile->postcode ?? '') }}" inputmode="numeric">
                @error('postcode')
                    <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div class="profile__group">
                <label class="profile__label" for="address">
                    住所
                </label>
                <input class="profile__input" id="address" type="text" name="address" value="{{ old('address', $user->profile->address ?? '') }}" autocomplete="street-address">
                @error('address')
                    <p class="profile__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 建物名 --}}
            <div class="profile__group">
                <label class="profile__label" for="building">
                    建物名
                </label>
                <input class="profile__input" id="building" type="text" name="building" value="{{ old('building', $user->profile->building ?? '') }}">
            </div>

            {{-- 更新ボタン --}}
            <div class="profile__actions">
                <button type="submit" class="profile__button">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection