<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>furima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <h1 class="header__brand">
                {{-- 会員登録ユーザー及びメール認証していない場合は認証画面へ誘導 --}}
                {{-- 認証済みユーザー,未ログインユーザーはロゴよりトップへ遷移できる --}}
                <a class="header__brand-link" href="{{ auth()->check() && !auth()->user()->hasVerifiedEmail()? route('verification.notice'): route('index') }}">
                    <img class="header__brand-logo" src="{{ asset('images/logo.png') }}" alt="COACHTECH">
                </a>
            </h1>

            {{-- ヘッダーの条件分岐 --}}
            @if(!request()->routeIs('login', 'register','verification.notice'))
                <form class="header__search" action={{ url('/') }} method="GET">
                    <input class="header__search-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                        @if(!empty($tab))
                            <input type="hidden" name="tab" value="{{ $tab }}">
                        @endif
                </form>

                <nav class="header__nav">
                    {{-- ログイン、ログアウト表示を切り替え --}}
                    @auth
                        <form class="header__logout" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="header__logout-button" type="submit">ログアウト</button>
                        </form>
                    @else
                        <a class="header__login-button" href="{{ route('login') }}">ログイン</a>
                    @endauth

                    <a class="header__link" href="{{ route('mypage.index') }}">マイページ</a>
                    <a class="header__sell" href="{{ route('products.sell') }}">出品</a>
                </nav>
            @endif
        </div>
    </header>

	<main class="content">
		@yield('content')
	</main>

    @yield('js')
</body>
</html>