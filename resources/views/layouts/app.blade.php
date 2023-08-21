<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('layouts.styles')
    @yield('styles')
</head>

<body>
    <div id="app" style="background-color: #191A19;">
        <nav class="navbar navbar-expand-md shadow-sm"
            style="background-color: #191A19; position: fixed; top: 0; left: 0; width: 100%; z-index: 1;">
            <div class="container" style="font-weight:bolder; font-size:17px">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ url('storage/images/Logo.png') }}" alt="{{ config('app.name', 'Laravel') }}"
                        width="75px">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @if (Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'user'))
                            <a class="nav-link nav-dashboard nav-link-color" id="link1"
                                href="{{ route('admindashboard') }}" style="color: #F6F1E9">
                                Dashboard
                            </a>
                        @endif
                        <a class="nav-link nav-home nav-link-color" id="link2" href="{{ route('home') }}"
                            style="color: #F6F1E9;">
                            Home
                        </a>
                        <a class="nav-link nav-location nav-link-color" id="link3"
                            href="{{ route('locations', 'all') }}" style="color: #F6F1E9">
                            Locations
                        </a>
                        <a class="nav-link nav-drivers nav-link-color" id="link4"
                            href="{{ route('drivers', 'all') }}" style="color: #F6F1E9">
                            Drivers
                        </a>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        <li class="nav-item search-item">
                            <form action="{{ route('global.search') }}" method="POST">
                                @csrf
                                <div class="form-group global-search" style="width: 400px">
                                    <div class="ui-widget" style="width: 400px">
                                        <input id="tags" class="form-control" type="text"
                                            placeholder="Input keywords" name="search">
                                    </div>
                                    <button class="btn btn-warning btn-sm" type="submit"
                                        style="height: 40px; margin-left:-52px;">Search</button>
                                </div>
                            </form>
                        </li>
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item ">
                                    <a class="nav-link nav-link-color" href="{{ route('login') }}" style="color: #F6F1E9"
                                        id="link5">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link nav-link-color" href="{{ route('register') }}"
                                        style="color: #F6F1E9" id="link6">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item" style="height: 0px; margin: 0 50px">
                                <a href="{{ route('viewusergarage') }}">
                                    <div class="garage-icon-container">
                                        <img src="{{ asset('storage/images/Garage.png') }}" alt="garage"
                                            class="garage-icon">
                                        <i class="fa-solid fa-garage"></i>
                                        @if (Session::get('garage' . Auth::user()->id))
                                            @if (count(Session::get('garage' . Auth::user()->id)))
                                                <span class="garage-count">
                                                    {{ count(Session::get('garage' . Auth::user()->id)) }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </a>
                            </li>
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                @if (Auth::user()->customer && Auth::user()->customer->image_path)
                                    <img src="{{ Auth::user()->customer->image_path }}" width="40px"
                                        style="border-radius: 50%; object-fit:cover" height="40px">
                                @else
                                    {{ Auth::user()->name }}
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"
                                style="right: 150px; top:80px; z-index: 1;">
                                <a class="dropdown-item" href="{{ route('pendings') }}">Bookings</a>
                                <a class="dropdown-item" href="{{ route('viewprofile', Auth::user()->id) }}">
                                    Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @include('layouts.scripts')
    @yield('scripts')
</body>

</html>
