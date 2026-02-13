@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell">
    <div class="sell__header">
        <h1 class="sell__title">商品の出品</h1>
    </div>

    <div class="sell__card">
        <form class="sell__form" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
            {{-- 商品画像 --}}
            <section class="sell__section">
                <h2 class="sell__item-label">商品画像</h2>
                <div class="sell__image">
                    <div class="sell__image-drop">
                        <label class="sell__image-button" for="image">画像を選択する</label>
                        <input class="sell__image-input" id="image" type="file" name="image" accept="image/*">
                    </div>
                    @error('image')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- 商品の詳細 --}}
            <section class="sell__section">
                <h2 class="sell__section-title">商品の詳細</h2>

                <div class="sell__field">
                    <div class="sell__label">カテゴリー</div>
                    <div class="sell__category-list">
                        @foreach ($categories as $category)
                            <input class="sell__category-input" type="checkbox" name="category_ids[]" id="category_{{ $category->id }}" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                            <label class="sell__category-pill" for="category_{{ $category->id }}">
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('category_ids')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sell__field">
                    <label class="sell__label" for="condition">商品の状態</label>
                    <div class="sell__select-wrap">
                        <select class="sell__select" id="condition" name="condition">
                            {{-- 選択してくださいはプルダウンでは表示させない --}}
                            <option value="" disabled hidden {{ old('condition') ? '' : 'selected' }}>
                                選択してください
                            </option>
                            @foreach($conditions as $value => $label)
                                <option value="{{ $value }}" {{ old('condition') == (string)$value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('condition')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- 商品名と説明 --}}
            <section class="sell__section">
                <h2 class="sell__section-title">商品名と説明</h2>

                <div class="sell__field">
                    <label class="sell__label" for="name">商品名</label>
                    <input class="sell__input" id="name" name="name" type="text" value="{{ old('name') }}">
                    @error('name')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sell__field">
                    <label class="sell__label" for="brand_name">ブランド名</label>
                    <input class="sell__input" id="brand_name" name="brand_name" type="text" value="{{ old('brand_name') }}">
                </div>

                <div class="sell__field">
                    <label class="sell__label" for="description">商品の説明</label>
                    <textarea class="sell__textarea" id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sell__field">
                    <label class="sell__label" for="price">販売価格</label>
                    <div class="sell__price-row">
                        <span class="sell__price-mark">¥</span>
                        <input class="sell__input sell__price-input" id="price" name="price" type="text" inputmode="numeric" value="{{ old('price') }}">
                    </div>
                    @error('price')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <div class="sell__actions">
                <button class="sell__button" type="submit">出品する</button>
            </div>
        </form>
    </div>
</div>
@endsection