@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail">
	<div class="detail__inner">

		{{-- 左：画像 --}}
		<div class="detail__media">
            @php
                $path = $product->image_path;
                if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                    $src = $path;
                } else {
                    $src = asset('storage/' . $path);
                }
            @endphp

            <img src="{{ $src }}" alt="{{ $product->name }}">
		</div>

		{{-- 右：情報 --}}
		<div class="detail__info">
            <div class="detail__summary">
                <h1 class="detail__name">{{ $product->name }}</h1>
                <p class="detail__brand">{{ $product->brand_name ?? '' }}</p>

                <p class="detail__price">
					<span class="detail__currency">¥</span>
					<span class="detail__amount">{{ number_format($product->price) }}</span>
                    <span class="detail__tax">(税込)</span>
                </p>

                {{-- いいね / コメント（表示だけの枠） --}}
                <div class="detail__meta">
                    <button
                        type="button"
                        class="detail__meta-btn js-like-btn {{ ($isLiked ?? false) ? 'is-liked' : '' }}"
                        data-like-url="{{ route('likes.store', $product) }}"
                        data-unlike-url="{{ route('likes.destroy', $product) }}"
                        data-csrf="{{ csrf_token() }}"
                        {{-- 未ログインでは押せなくする --}}
                        @guest disabled @endguest
                    >
                        <img class="detail__icon-img detail__icon-img--off"
                            src="{{ asset('images/heartlogo_default.png') }}"
                            alt="いいね">
                        <img class="detail__icon-img detail__icon-img--on"
                            src="{{ asset('images/heartlogo_pink.png') }}"
                            alt="いいね済み">
                        <span class="detail__count js-like-count">{{ $likesCount ?? 0 }}</span>
                    </button>

                    <div class="detail__meta-item">
                        <img class="detail__icon-img"
                            src="{{ asset('images/hukidashi_logo.png') }}"
                            alt="コメント">
                        <span class="detail__count">{{ $commentsCount ?? 0 }}</span>
                    </div>
                </div>

                {{-- ProductController(show)の内容に応じてURL先を指定 --}}
                @if($showBuyButton && $buyUrl)
                    <a class="detail__buy-btn" href="{{ $buyUrl }}">購入手続きへ</a>
                @endif
            </div>

            {{-- 商品説明 --}}
            <section class="detail__section">
                <h2 class="detail__section-title">商品説明</h2>
                <p class="detail__desc">{{ $product->description }}</p>
            </section>

            {{-- 商品の情報 --}}
            <section class="detail__section">
                <h2 class="detail__section-title">商品の情報</h2>

                <div class="detail__info-table">
                    <div class="detail__info-row">
                        <div class="detail__info-head">カテゴリー</div>
                        <div class="detail__info-body">
                            <div class="detail__tags">
                                @foreach($product->categories as $category)
                                    <span class="detail__tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="detail__info-row">
                        <div class="detail__info-head">商品の状態</div>
                        <div class="detail__info-body">
                            <span class="detail__condition">{{ $conditions[$product->condition] ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- コメント --}}
            <section class="detail__section detail__section--comment">
                <h2 class="detail__section-title">コメント({{ $commentsCount ?? 0 }})</h2>
                {{-- コメント一覧 --}}
                <div class="detail__comments">
                    @forelse($comments ?? [] as $comment)
                        <div class="detail__comment">
                            <div class="detail__comment-avatar">
                                @if(!empty($comment->user->profile->profile_image_path))
                                    <img src="{{ asset('storage/' .$comment->user->profile->profile_image_path) }}" alt="ユーザー画像">
                                @else
                                    <div class="detail__comment-avatar-placeholder"></div>
                                @endif
                            </div>

                            <p class="detail__comment-name">
                                {{ $comment->user->profile->nickname ?? $comment->user->name ?? '' }}
                            </p>

                            <p class="detail__comment-text">{{ $comment->body }}</p>
                        </div>

                    @empty
                        <p class="detail__comment-empty"></p>
                    @endforelse
                </div>

                {{-- コメント投稿 --}}
                <form class="detail__comment-form" method="POST" action="{{ route('comments.store', $product) }}">
                    @csrf
                    <label class="detail__comment-label" for="body">商品へのコメント</label>
                    <textarea class="detail__comment-textarea" id="body" name="body">{{ old('body') }}</textarea>

                    @error('body')
                        <p class="detail__comment-error">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="detail__comment-submit">コメントを送信する</button>
                </form>
            </section>
		</div>
	</div>
</div>
@endsection

@section('js')
	<script src="{{ asset('js/detail.js') }}"></script>
@endsection