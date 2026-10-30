
@extends('layouts.jetsi')

@section('title', 'Home')
@section('nav', 'home')

@section('content')

<!-- HERO -->
<section class="hero">
    <div class="container hero-inner">
      <div class="hero-logos">
        <img class="hero-logo" src="{{ asset('img/home-logo.png') }}" alt="JETSI logo" width="200" height="200">
        <img class="hero-logo" src="{{ asset('img/Jetsi2.png') }}" alt="JETSI journal logo" width="200" height="200">
      </div>
      <h1>Journal of Engineering Technology<br>and Sustainable Innovation</h1>
      <p class="tagline">High-quality scholarly research on sustainable engineering, renewable energy and emerging technology.</p>

        <form class="search-card" action="{{ route('search') }}" role="search">
            <div class="field">
                <label for="keyword">Keywords</label>
                <input id="keyword" name="keyword" type="text" placeholder="e.g. solar, grid, concrete">
            </div>
            <div class="field">
                <label for="author">Author name</label>
                <input id="author" name="author" type="text">
            </div>
            <div class="field">
                <label for="issue">Issue</label>
                <input id="issue" name="issue" type="text">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</section>

<main id="main">

    <!-- FACTS STRIP -->
    <section class="facts" aria-label="Journal information">
        <div class="container facts-grid">
            <div>
                <span class="fact-label">Publisher</span>
                <span class="fact-value">University of Calabar</span>
            </div>
        </div>
    </section>

    <!-- CURRENT ISSUE -->
    @if ($current_issue)
    <section class="section">
        <div class="container">
            <article class="current-issue card">
                <p class="eyebrow">Current issue</p>

                <div class="current-body">
                    <div class="cover cover-large" aria-hidden="true">
                        <span>Vol {{ $current_issue->volume }}<br>No {{ $current_issue->number }}</span>
                    </div>

                    <div>
                        <h2>
                            <a href="{{ route('issues.show', $current_issue->slug) }}">
                                {{ $current_issue->title }}
                            </a>
                        </h2>

                        <p class="meta">
                            Volume {{ $current_issue->volume }}
                            &middot; Issue {{ $current_issue->number }}
                        </p>

                        @if ($current_issue->pages)
                        <p class="meta">Page Nos. {{ $current_issue->pages }}</p>
                        @endif

                        <p class="meta">
                            Online since
                            {{ $current_issue->created_at
                                ? \Carbon\Carbon::parse($current_issue->created_at)->toFormattedDateString()
                                : 'N/A' }}
                        </p>

                        <a class="btn btn-primary"
                           href="{{ route('issues.show', $current_issue->slug) }}">
                            View issue
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </section>
    @endif

    <!-- RECENT ISSUES -->
    @if (isset($issues) && $issues->count())
    <section class="section section-alt">
        <div class="container">
            <div class="section-head">
                <h2>Recent issues</h2>
                <a class="link-arrow" href="{{ route('issues.index') }}">
                    All issues
                    <span class="material-icons">arrow_forward</span>
                </a>
            </div>

            <div class="issue-grid">
                @foreach ($issues->take(3) as $issue)
                    @include('partials.jetsi_issue_card', ['issue' => $issue])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- POPULAR ARTICLES -->
    @if (isset($articles) && $articles->count())
    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>Popular articles</h2>
            </div>

            <ol class="article-list">
                @foreach ($articles as $article)
                <li class="article card">
                    <span class="rank" aria-hidden="true">{{ $loop->iteration }}</span>

                    <div class="article-body">
                        <h3>
                            <a href="{{ route('articles.show', $article->slug) }}">
                                {{ $article->title }}
                            </a>
                        </h3>

                        <p class="authors">{{ $article->authors }}</p>

                        <p class="meta">
                            @if ($article->issue)
                                Vol. {{ $article->issue->volume }}
                                &middot; Issue {{ $article->issue->number }},
                                {{ $article->issue->year }}
                            @endif

                            @if ($article->pages)
                                &middot; pp {{ $article->pages }}
                            @endif
                        </p>
                    </div>

                    <span class="views" title="Views">
                        <span class="material-icons">visibility</span>
                        {{ number_format($article->views ?? 0) }}
                    </span>
                </li>
                @endforeach
            </ol>
        </div>
    </section>
    @endif

    <!-- ABOUT AND SUBMISSION -->
    <section class="aims-band" aria-labelledby="about-heading">
        <div class="container aims-inner">
            <div class="aims-text">
                <h2 id="about-heading">About us</h2>
                <p>
                    JETSI provides a platform for high-quality scholarly articles
                    that address contemporary challenges and emerging frontiers
                    in engineering, with a strong emphasis on sustainability,
                    renewable energy, climate change mitigation, technological
                    advancement, and sustainable industrial systems.
                </p>
            </div>

            <a class="btn btn-primary" href="{{ route('guideline') }}">
                <span class="material-icons">unarchive</span>
                Submit article
            </a>
        </div>
    </section>

    <!-- NEWSLETTER -->
    <section class="section newsletter">
        <div class="container narrow">
            <h2>New publication? Get notified.</h2>
            <p>Sign up to receive email notifications on new publications.</p>

            <form id="subscribe"
                  class="newsletter-form"
                  action="{{ url('/api/subscribe') }}"
                  method="post">

                {{ csrf_field() }}

                <div class="field">
                    <label for="sub-name">Full name</label>
                    <input id="sub-name" name="name" type="text" required>
                </div>

                <div class="field">
                    <label for="sub-email">Email address</label>
                    <input id="sub-email" name="email" type="email" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <span class="material-icons">notifications</span>
                    Subscribe
                </button>
            </form>

            <p id="subscribe-message"
               class="newsletter-message"
               role="status"
               aria-live="polite"></p>
        </div>
    </section>

</main>

@endsection

@section('scripts')
<script>
(function () {
    var form = document.getElementById('subscribe');
    var message = document.getElementById('subscribe-message');

    if (!form || !window.fetch) {
        return;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var button = form.querySelector('button[type="submit"]');
        var tokenElement = document.querySelector('meta[name="csrf-token"]');
        var token = tokenElement ? tokenElement.getAttribute('content') : '';

        button.disabled = true;

        fetch(form.getAttribute('action'), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return {
                    ok: response.ok,
                    data: data
                };
            });
        })
        .then(function (result) {
            if (result.ok && result.data.msg) {
                form.style.display = 'none';
                message.textContent = result.data.msg;
                return;
            }

            var text = 'Sorry, we could not subscribe you. Please check your details and try again.';

            if (result.data && result.data.errors) {
                var first = Object.keys(result.data.errors)[0];

                if (first) {
                    text = result.data.errors[first][0];
                }
            }

            message.textContent = text;
            button.disabled = false;
        })
        .catch(function () {
            message.textContent = 'Sorry, something went wrong. Please try again.';
            button.disabled = false;
        });
    });
})();
</script>
@endsection
