@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">
    {{-- プロフィールヘッダー --}}
    <section class="mypage__profile">
        <div class="mypage__profile-row">
            <div class="mypage__avatar">
                @if($avatarSrc)
                    <img class="mypage__avatar-img" src="{{ $avatarSrc }}" alt="プロフィール画像">
                @else
                    <div class="mypage__avatar-placeholder" aria-label="プロフィール画像（未設定）"></div>
                @endif
            </div>

            <p class="mypage__username">{{ $user->profile->nickname ?? $user->name }}</p>

            <a href="{{ route('mypage.profile', ['from' => 'index']) }}" class="mypage__edit">
                プロフィールを編集
            </a>
        </div>
    </section>

    {{-- タブ --}}
    <div class="mypage__tabs-bar">
        <div class="mypage__tabs">
            <a href="{{ route('mypage.index', ['page' => 'sell']) }}" class="mypage__tab {{ $page === 'sell' ? 'is-active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('mypage.index', ['page' => 'buy']) }}" class="mypage__tab {{ $page === 'buy' ? 'is-active' : '' }}">
                購入した商品
            </a>
        </div>
    </div>

    {{-- 商品グリッド --}}
    <div class="products">
        <div class="products__grid">
            @forelse ($products as $product)
                <a class="products__card" href="{{ route('products.detail', $product->id) }}">
                    <div class="products__image">
                        @if($product->order)
                            <span class="products__sold-badge">Sold</span>
                        @endif

                        @php
                            $path = $product->image_path;
                            $src = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                                ? $path
                                : asset('storage/' . $path);
                        @endphp

                        <img src="{{ $src }}" alt="{{ $product->name }}">
                    </div>

                    <p class="products__name">{{ $product->name }}</p>
                </a>
            @empty
                <p class="products__empty"></p>
            @endforelse
        </div>
    </div>
</div>
@endsection