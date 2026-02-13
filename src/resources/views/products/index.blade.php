@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="products">
    <div class="products__tabs-bar">
        <div class="products__tabs">
            <a href="{{ url('/') }}?keyword={{ request('keyword') }}" class="products__tab {{ empty($tab) ? 'is-active' : '' }}">おすすめ</a>
            <a href="{{ url('/') }}?tab=mylist&keyword={{ request('keyword') }}" class="products__tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
        </div>
    </div>

    <div class="products__grid">
        @forelse ($products as $product)
            <a class="products__card" href="{{ route('products.detail', $product->id) }}">
                <div class="products__image">
                    {{-- 画像の上にSoldを表示 --}}
                    @if($product->order)
                        <span class="products__sold-badge">Sold</span>
                    @endif

                    {{-- 外部URLでもローカルパスでも画像が出るように --}}
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

                <p class="products__name">{{ $product->name }}</p>
            </a>
        {{-- 出品が何もない場合は表示されない --}}
        @empty
            <p class="products__empty"></p>
        @endforelse
    </div>
</div>
@endsection