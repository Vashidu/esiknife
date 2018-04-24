<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <title>@yield('title') || ESI Knife || Eve Online</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <!-- Font Awesome -->
        <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>

        <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.0/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-A0hm/Pn0Gvk8w7szlEuTOZrIyQCCBNXQF9ccseoCI36nWRfMEAapo5UJ56zLPvPw" crossorigin="anonymous">

        <!-- Custom styles for this template -->
        <link href="{{ url('css/app.css') }}" rel="stylesheet">

        @yield('css')
    </head>

    <body>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">ESI Mail</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto mr-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        @if(Auth::check())
                            <li class="nav-item dropdown ml-3">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Hello {{ collect(explode(' ', Auth::user()->info->name))->first() }} <b class="caret"></b>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                  {{-- <a class="dropdown-item" href="{{ route('mail.list') }}"> My Mail</a>
                                  <a class="dropdown-item" href="{{ route('settings.index') }}"> My Settings</a> --}}
                                  {{-- <a class="dropdown-item" href="#"> Alt Hotswap</a> --}}
                                  <div class="dropdown-divider"></div>
                                  <a class="dropdown-item" href="{{ route('auth.logout') }}"> Logout</a>
                                </div>
                            </li>
                        @else
                            <a href="{{ route('auth.login') }}" class="nav-link">Login</a>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        <hr>
        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12 text-center">
                    Brought to you by <a href="https://evewho.com/pilot/David+Davaham">David Davaham</a><br />
                    <p>Copyright &copy; <a href="mailto:ddouglas@douglaswebdev.net">David Douglas</a> 2018</p>
                    {{-- - {{ \Carbon\Carbon::now()->format('Y') }} --}}
                </div>
            </div>
        </footer>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        @yield('js')

    </body>
</html>