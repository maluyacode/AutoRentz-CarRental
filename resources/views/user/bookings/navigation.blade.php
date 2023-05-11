<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script>



    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .nav-item:hover{
            background-color: #454545;
            border-radius: 5px;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav" style="font-size: 18px">
            <ul class="navbar-nav" style="margin:auto">
                <div style="display: flex; flex-direction:row; margin-right:80px">
                    <li class="nav-item active" style="margin: 0px 30px">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item" style="margin: 0px 30px">
                        <a class="nav-link" href="{{ route('viewprofile', Auth::user()->id) }}">Profile</a>
                    </li>
                    <li class="nav-item" style="margin: 0px 30px">
                        <a class="nav-link" href="{{ route('viewusergarage') }}">Garage</a>
                    </li>
                </div>
                <li class="nav-item active" style="margin: 0px 30px">
                    <a class="nav-link" href="{{ route('pendings') }}">Pendings</a>
                </li>
                <li class="nav-item" style="margin: 0px 30px">
                    <a class="nav-link" href="{{ route('confirmed') }}">Confirmed</a>
                </li>
                <li class="nav-item" style="margin: 0px 30px">
                    <a class="nav-link" href="{{ route('finished') }}">Finished</a>
                </li>
                <li class="nav-item" style="margin: 0px 30px">
                    <a class="nav-link" href="{{ route('displaycancelled') }}">Cancelled</a>
                </li>

            </ul>
        </div>
    </nav>
    <main>
        @yield('booking')
    </main>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>
