<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('website/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/stellarnav.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css"/>
    <link rel="stylesheet" href="{{ asset('website/css/style.css?v='.time()) }}">
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
</head>
<body>
    <header>
        <div class="header-area">
            <div class="main-header header-sticky">
                <div class="container">
                    <div class="menu-wrapper d-flex align-items-center justify-content-between">
                        <div class="left-content d-flex align-items-center">
                            <div class="logo">
                                <a href="{{ route('home') }}"><img src="{{ asset('website/images/logo-transparent.png') }}" alt="logo"></a>
                            </div>
                        </div>
                        <div class="main-menu">
                            <nav class="stellarnav">
                                <ul>
                                    @if (Route::has('home'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a>
                                        </li>
                                    @endif

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('contact_us') }}">{{ __('Contact us') }}</a>
                                    </li>

                                    @guest

                                        @if (Route::has('login'))
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                            </li>
                                        @endif

                                    @else
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" href="#">
                                                {{ Auth::user()->name }}
                                            </a>
                                        </li> -->

                                        <!-- <li class="nav-item">
                                            <a class="nav-link" href="{{ route('logout') }}">{{ __('Logout') }}</a>
                                        </li> -->

                                        <li class="nav-item dropdown">
                                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                <i class="fa fa-user" aria-hidden="true"></i> {{ Auth::user()->name }}
                                            </a>

                                            <ul class="dropdown-menu dropdown-menu-end submenu" aria-labelledby="navbarDropdown">
                                                <li><a class="dropdown-item" href="{{ route('logout') }}"
                                                   onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                                                    {{ __('Logout') }}
                                                </a></li>

                                                <li><form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form></li>
                                            </ul>
                                        </li>
                                    @endguest
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="footer-wrapper">
            <div class="footer-area footer-padding">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-8">
                            <div class="single-footer-caption">
                                <div class="single-footer-caption">
                                    <div class="footer-logo">
                                        <a href="{{ route('home') }}"><img src="{{ asset('website/images/logo-transparent.png') }}" alt=""></a>
                                    </div>
                                    <div class="footer-tittle">
                                        <div class="footer-pera">
                                            <p>Land behold it created good saw after she'd Our set living. Signs midst dominion creepeth.</p>
                                        </div>
                                        <div class="footer-social">
                                            <a href="#"><i class="fab fa-twitter-square"></i></a>
                                            <a href="#"><i class="fab fa-facebook-square"></i></a>
                                            <a href="#"><i class="fab fa-linkedin"></i></a>
                                            <a href="#"><i class="fab fa-pinterest-square"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                            <div class="single-footer-caption">
                                <div class="footer-tittle">
                                    <h4>Quick Links</h4>
                                    <ul>
                                        <li><a href="{{ route('home') }}">Home</a></li>
                                        <li><a href="{{ route('contact_us') }}">Contact</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-8 col-sm-6">
                            <div class="single-footer-caption">
                                <div class="footer-tittle">
                                    <h4>Cakes</h4>
                                    <ul>
                                        <li><a href="#">Blackforest</a></li>
                                        <li><a href="#">Bodhubon</a></li>
                                        <li><a href="#">Rongdhonu</a></li>
                                        <li><a href="#">Meghrong</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-8">
                            <div class="single-footer-caption">
                                <div class="footer-tittle">
                                    <h4>Contact Us</h4>
                                    <p>76/A, Green Lane, Dhanmondi, NYC</p>
                                </div>
                                <ul>
                                    <li class="number"><a href="#">+10 (78) 738-9083</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom-area">
                <div class="container">
                    <div class="footer-border">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="footer-copy-right text-center">
                                    <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with by <a href="index.html" target="_blank" rel="nofollow noopener">Nxsol</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('website/js/modernizr-3.5.0.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.min.js') }}"></script>
    <script src="{{ asset('website/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('website/js/stellarnav.min.js') }}"></script>
    <script src="{{ asset('website/js/wow.min.js') }}"></script>
    <!-- <script src="{{ asset('website/js/slick.min.js') }}"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
    <script src="{{ asset('website/js/main.js?v='.time()) }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script src="{{ asset('website/js/rescalendar.js?v='.time()) }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    @yield('jQuery')
</body>
</html>
