<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css?a=1') }}" rel="stylesheet">
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (!Auth::guest() && Auth::user()->role > \App\User::ROLE_GUEST)
                        @if (\App\User::isAdmin())
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Домены<span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="/domain/create">Добавить новые</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Письма<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if (\App\User::isModerator())
                                    <li>
                                        <a href="/moderator/emails">Модератор</a>
                                    </li>
                                @endif

                                @if (\App\User::isExternalUser())
                                    <li>
                                        <a href="/random/email">Отправка</a>
                                    </li>
                                @endif

                                @if (\App\User::isAdmin())
                                    <li>
                                        <a href="/email/statistic">Статистика</a>
                                    </li>
                                @endif
                                @if (\App\User::isModerator())

                                    <li>
                                        <a href="/email/moderation_log">Лог модерации</a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Субдомены<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if (\App\User::isModerator())

                                    <li>
                                        <a href="/moderator/subdomains">Модератор</a>
                                    </li>
                                @endif
                                @if (\App\User::isExternalUser())

                                    <li>
                                        <a href="/random/manualSubdomain">Поиск контактов</a>
                                    </li>
                                    <li>
                                        <a href="/subdomain/statistic">Статистика</a>
                                    </li>
                                @endif

                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Регистраторы<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if (\App\User::isModerator())
                                    <li>
                                        <a href="/moderator/links">Модератор</a>
                                    </li>
                                @endif

                                @if (\App\User::isExternalUser())
                                    <li>
                                        <a href="/random/manualDomain">Поиск контактов</a>
                                    </li>
                                    <li>
                                        <a href="/random/link">Отправка</a>
                                    </li>
                                    <li>
                                        <a href="/link/statistic">Статистика</a>
                                    </li>
                                    <li>
                                        <a href="/manual/found_log">Лог поиска контактов</a>
                                    </li>
                                @endif
                                @if (\App\User::isModerator())
                                    <li>
                                        <a href="/link/moderation_log">Лог модерации</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        @if (\App\User::isAdmin())
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Очтеты<span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="/moderator/report">Модерация</a>
                                    </li>
                                    <li>
                                        <a href="/manual/report">Поиск контактов</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div id="page_title">
        {{ $title ?? '' }}
    </div>

    @yield('content')

</div>
<!-- Scripts -->
</body>
</html>
