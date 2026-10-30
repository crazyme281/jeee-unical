<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="Author" content="Djade System">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>The Journal of Engineering Technology and Sustainable Innovation - @yield('title')</title>

  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="{{ asset('css/jetsi.css') }}">

  @yield('styles')
</head>
<body>
@php
  // Which nav item to highlight. Each page sets it with @section('nav', 'home'|'about'|'issues'|'subscribe'|'contact').
  $current_nav = trim($__env->yieldContent('nav'));

  $nav_items = [
      'home'      => ['Home', url('/')],
      'about'     => ['About us', route('about')],
      'issues'    => ['Issues', route('issues.index')],
      'subscribe' => ['Subscribe', route('subscription')],
      'contact'   => ['Contact us', route('contact')],
  ];

  // Message box under the navbar. Controllers flash ['type' => ..., 'message' => ...] under 'status'.
  $flash_icons = ['success' => 'check_circle', 'info' => 'info', 'warning' => 'warning', 'danger' => 'error'];
  $flash       = session('status');
  $show_flash  = is_array($flash) && isset($flash['message']) && is_string($flash['message']) && $flash['message'] !== '';
  $flash_type  = ($show_flash && isset($flash['type']) && is_string($flash['type'])) ? $flash['type'] : 'info';
  if ($flash_type === 'error') { $flash_type = 'danger'; }
  if (!isset($flash_icons[$flash_type])) { $flash_type = 'info'; }
@endphp
  <a class="skip-link" href="#main">Skip to main content</a>

  <!-- NAVBAR -->
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="{{ url('/') }}">
        <img src="{{ asset('img/Jetsi2.png') }}" alt="JETSI logo">
        <span class="brand-name">JETSI<small>Journal of Engineering Technology<br>and Sustainable Innovation</small></span>
      </a>
      <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="main-nav">
        <span class="material-icons">menu</span>
      </button>
      <nav id="main-nav" class="main-nav" aria-label="Main">
        <ul>
          @foreach ($nav_items as $key => $item)
          <li><a href="{{ $item[1] }}"{!! $key === $current_nav ? ' aria-current="page"' : '' !!}>{{ $item[0] }}</a></li>
          @endforeach
          <li><a class="btn btn-outline" href="{{ route('guideline') }}"><span class="material-icons">unarchive</span> Submit article</a></li>

          <!-- Authentication Links -->
          @guest
          <li><a class="btn btn-primary" href="{{ route('login') }}">Login</a></li>
          @else
          <li class="user-menu">
            <button type="button" class="user-toggle" aria-expanded="false" aria-controls="user-dropdown">
              <span class="material-icons" aria-hidden="true">account_circle</span>
              {{ Auth::user()->first_name }}
              <span class="material-icons" aria-hidden="true">arrow_drop_down</span>
            </button>
            <ul id="user-dropdown" class="user-dropdown">
              <li><a href="{{ route('manuscripts.create') }}">Submit a Manuscript</a></li>
              <li><a href="{{ route('manuscripts.index') }}">My Submissions</a></li>
              <li class="divider" role="separator"></li>
              <li><a href="{{ route('profile') }}">My Profile</a></li>
              <li><a href="{{ route('account') }}">My Account</a></li>
              <li><a href="{{ route('subscriptions') }}">My Subscriptions</a></li>
              <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                </form>
              </li>
            </ul>
          </li>
          @endguest
        </ul>
      </nav>
    </div>
  </header>

  @if ($show_flash)
  <div class="container">
    <div class="flash flash-{{ $flash_type }}" role="{{ in_array($flash_type, ['warning', 'danger'], true) ? 'alert' : 'status' }}">
      <span class="material-icons" aria-hidden="true">{{ $flash_icons[$flash_type] }}</span>
      <p>{{ $flash['message'] }}</p>
      <button type="button" class="flash-close" aria-label="Dismiss message" onclick="this.parentNode.parentNode.removeChild(this.parentNode)"><span class="material-icons" aria-hidden="true">close</span></button>
    </div>
  </div>
  @endif

  @yield('content')

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <img src="{{ asset('img/Jetsi2.png') }}" alt="JETSI logo">
        <p>Journal of Engineering Technology and Sustainable Innovation</p>
      </div>
      <nav aria-label="About">
        <h4>About</h4>
        <ul><li><a href="{{ route('about') }}">About us</a></li></ul>
      </nav>
      <nav aria-label="Authors">
        <h4>Authors</h4>
        <ul><li><a href="{{ route('guideline') }}">Submit article</a></li></ul>
      </nav>
      <nav aria-label="Issues">
        <h4>Issues</h4>
        <ul><li><a href="{{ route('issues.index') }}">Issues</a></li></ul>
      </nav>
      <nav aria-label="Contact">
        <h4>Contact</h4>
        <ul><li><a href="{{ route('contact') }}">Contact us</a></li></ul>
      </nav>
    </div>
    <div class="footer-bottom">
      <div class="container">&copy; {{ date('Y') }} Journal of Engineering Technology and Sustainable Innovation. University of Calabar.</div>
    </div>
  </footer>

  <script>
    var t = document.querySelector('.nav-toggle'), n = document.getElementById('main-nav');
    t.addEventListener('click', function () {
      var open = n.classList.toggle('open');
      t.setAttribute('aria-expanded', open);
    });

    // Signed-in user menu
    var ut = document.querySelector('.user-toggle'), ud = document.getElementById('user-dropdown');
    if (ut && ud) {
      ut.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = ud.classList.toggle('open');
        ut.setAttribute('aria-expanded', open);
      });
      document.addEventListener('click', function (e) {
        if (!ud.contains(e.target)) { ud.classList.remove('open'); ut.setAttribute('aria-expanded', 'false'); }
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { ud.classList.remove('open'); ut.setAttribute('aria-expanded', 'false'); }
      });
    }
  </script>

  @yield('scripts')
</body>
</html>
