@extends('layouts.app')

@section('css')
	<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
	<div class="address">
		<div class="address__header">
			<h1 class="address__title">住所の変更</h1>
		</div>

		<div class="address__card">
			<form action="{{ route('purchase.address.store', $product) }}" class="address__form" method="POST" enctype="multipart/form-data">
				@csrf

				{{-- 郵便番号 --}}
				<div class="address__group">
					<label class="address__label" for="postcode">
						郵便番号
					</label>
					<input class="address__input" id="postcode" type="text" name="postcode" value="" inputmode="numeric">
					@error('postcode')
						<p class="address__error">{{ $message }}</p>
					@enderror
				</div>

				{{-- 住所 --}}
				<div class="address__group">
					<label class="address__label" for="address">
						住所
					</label>
					<input class="address__input" id="address" type="text" name="address" value="" autocomplete="street-address">
					@error('address')
						<p class="address__error">{{ $message }}</p>
					@enderror
				</div>

				{{-- 建物名 --}}
				<div class="address__group">
					<label class="address__label" for="building">
						建物名
					</label>
					<input class="address__input" id="building" type="text" name="building" value="">
				</div>

				{{-- 更新ボタン --}}
				<div class="address__actions">
					<button type="submit" class="address__button">更新する</button>
				</div>
			</form>
		</div>
	</div>
@endsection