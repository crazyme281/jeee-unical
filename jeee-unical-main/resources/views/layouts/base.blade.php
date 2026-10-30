<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon.png">
        <link rel="icon" type="image/png" href="/img/favicon.png">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="Author" content="Djade System" />

        <title>The Journal of Engineering Technology and Sustainable Innovation - @yield('title')</title>

        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!--     Fonts and icons     -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

        <!-- CSS Files -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/material-kit.css') }}" rel="stylesheet"/>

        <link href="{{ asset('css/custom.css') }}" rel="stylesheet"/>

        @yield('styles')
    </head>
    <!-- Body starts here-->
    <body class="landing-page">

        <div class="wrapper">
		   @yield('splash')
		 <!-- Navbar Primary  -->
            <nav id="nav" class="navbar navbar-primary sticky-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-navbar-primary">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand brand-small" href="{{ url('/') }}"><img src="{{ asset('/img/favicon.png') }}" style="height: 50px; width: 85px;"></a>
                    </div>

                    <div class="collapse navbar-collapse" id="example-navbar-primary">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="@yield('about.active')">
                                <a href="./">
                                    Home
                                </a>
                            </li>
                            <li class="@yield('about.active')">
                                <a href="{{ route('about') }}">
                                    About us
                                </a>
                            </li>
                            <li class="@yield('issues.active')">
                                <a href="{{ route('issues.index') }}">
                                    Issues
                                </a>
                            </li>
                            <li class="@yield('guideline.active')">
                                <a href="{{ route('guideline') }}">
                                    <i class="material-icons">unarchive</i>
                                    Submit Article
                                </a>
                            </li>
                            <li class="@yield('subscription.active')">
                                <a href="{{ route('subscription') }}">
                                    <i class="material-icons">payment</i>
                                    Subscribe
                                </a>
                            </li>
                            <li class="@yield('contact.active')">
                                <a href="{{ route('contact') }}">
                                    Contact us
                                </a>
                            </li>

                            <!-- Authentication Links -->
                            @guest
                            <li class="@yield('auth.active')">
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    Login
                                    <div class="ripple-container"></div>
                                </a>
                            </li>
                            @else
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        <i class="material-icons">account_circle</i>
                                        {{ Auth::user()->first_name }} <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{ route('manuscripts.create') }}">
                                                Submit a Manuscript
                                                <div class="ripple-container"></div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('manuscripts.index') }}">
                                                My Submissions
                                                <div class="ripple-container"></div>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('profile') }}">
                                                My Profile
                                                <div class="ripple-container"></div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('account') }}">
                                                My Account
                                                <div class="ripple-container"></div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('subscriptions') }}">
                                                My Subscriptions
                                                <div class="ripple-container"></div>
                                            </a>
                                        </li>
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
                                @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar Primary -->
            <!-- Home Splash-->
         

           

            {{--Notification--}}
            @include('partials.notifications')
            <!-- Main Content -->
            @yield('content')

            <footer class="footer">
                <div class="container">
                    <nav class="pull-left">
                        <ul>
                            <li>
                                <a href="{{ route('about') }}">
                                    About us
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('issues.index') }}">
                                    Issues
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guideline') }}">
                                    Submit article
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}">
                                    Contact us
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="copyright pull-right">
                        &copy; <?php echo date("Y");?> Journal of Engineering Technology and Sustainable Innovation. University of Calabar.  </a>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Scripts -->
        <!--   Core JS Files   -->
        <script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/material.min.js') }}"></script>
        <script src="{{ asset('js/jquery.bootstrap.js') }}" type="text/javascript"></script>
        <script src="/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
        <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
        <script src="{{ asset('js/nouislider.min.js') }}" type="text/javascript"></script>

        <!-- Control Center for Material Kit: activating the ripples, parallax effects, scripts from the example pages etc -->
        <script src="{{ asset('js/material-kit.js') }}" type="text/javascript"></script>

        <script src="/js/front.js"></script>

        @yield('scripts')

        <script>
//            function fixNavBar() {
//                var navbar = $('#nav');
//                var window_top = $(window).scrollTop();
//                var div_top = $('#nav').offset().top;
//                if (window_top > div_top) {
//                    navbar.addClass('navbar-fixed-top');
//                } else {
//                    navbar.removeClass('navbar-fixed-top');
//                }
//
//            }
//
//            $(function () {
//                $(window).scroll(fixNavBar);
//                //fixNavBar();
//            });
        </script>
    </body>
</html>
