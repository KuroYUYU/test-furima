@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
	<div class="purchase">
		<div class="purchase__container">
			<form method="POST" action="{{ route('purchase.store', $product) }}" class="purchase__form">
				@csrf
				{{-- 左カラム --}}
				<main class="purchase__main">
					{{-- 商品概要 --}}
					<section class="purchase__product">
						<div class="purchase__product-media">
							@php
								$path = $product->image_path;
								if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
									$src = $path;
								} else {
									$src = asset('storage/' . $path);
								}
							@endphp

							<img class="purchase__product-image" src="{{ $src }}" alt="{{ $product->name }}">
						</div>

						<div class="purchase__product-summary">
							<h1 class="purchase__product-name">{{ $product->name }}</h1>
							<p class="purchase__product-price">
								<span class="purchase__product-currency">¥</span>
								<span class="purchase__product-amount">{{ number_format($product->price) }}</span>
							</p>
						</div>
					</section>

					{{-- 支払い方法 --}}
					<section class="purchase__section">
						<h2 class="purchase__section-title">支払い方法</h2>

						<div class="purchase__select-wrap">
							<select class="purchase__select" name="payment_method" id="payment_method">
								<option value="" disabled selected>選択してください</option>
								@foreach ($paymentMethods as $value => $label)
									<option value="{{ $value }}" {{ old('payment_method') == (string)$value ? 'selected' : '' }}>
										{{ $label }}
									</option>
								@endforeach
							</select>
						</div>

						@error('payment_method')
							<p class="purchase__error">{{ $message }}</p>
						@enderror
					</section>

					{{-- 配送先 --}}
					<section class="purchase__section">
						<div class="purchase__section-head">
							<h2 class="purchase__section-title">配送先</h2>
							<a class="purchase__link" href="{{ route('purchase.address.edit', $product) }}">変更する</a>
						</div>

						<div class="purchase__address">
							<p class="purchase__address-postcode">
								〒 {{ $shippingAddress['postcode'] ?? '' }}
							</p>
							<p class="purchase__address-text">
								{{ $shippingAddress['text'] ?? '' }}
							</p>
						</div>
					</section>
				</main>

				{{-- 右カラム --}}
				<aside class="purchase__side">
					<div class="purchase__side-card">
						<div class="purchase__side-row">
							<span class="purchase__side-label">商品代金</span>
							<span class="purchase__side-value">
								<span class="purchase__product-currency">¥</span>
								<span class="purchase__product-amount">{{ number_format($product->price) }}</span>
							</span>
						</div>

						<div class="purchase__side-row">
							<span class="purchase__side-label">支払い方法</span>
							<span class="purchase__side-value js-payment-label">未選択</span>
						</div>
					</div>

						<button class="purchase__submit-btn" type="submit">購入する</button>
				</aside>
			</form>
		</div>
	</div>
@endsection

@section('js')
	<script src="{{ asset('js/purchase.js') }}"></script>
@endsection