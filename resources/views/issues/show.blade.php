@extends('layouts.jetsi')

@section('title', 'Issue')
@section('nav', 'issues')

@section('content')

@php
  // The groups in the order they appear on the page.
  $article_groups = [
      'Original Articles' => $original_articles,
      'Review Articles'   => $review_articles,
      'Case Reports'      => $report_articles,
  ];
  $has_articles = false;
  foreach ($article_groups as $group_articles) {
      if (count($group_articles)) {
          $has_articles = true;
      }
  }
  $band = 0; // counts the groups that are shown, so the background can alternate
@endphp

  <div class="page-head">
    <div class="container"><h1>{{ $issue->title }} Issue</h1></div>
  </div>

  <main id="main">

    <!-- ISSUE DETAILS -->
    <section class="section">
      <div class="container">
        <div class="section-head">
          <h2>Issue details</h2>
          <a class="link-arrow" href="{{ route('issues.index') }}">All issues <span class="material-icons">arrow_forward</span></a>
        </div>
        <article class="current-issue card">
          <div class="current-body">
            <div class="cover cover-large" aria-hidden="true"><span>Vol {{ $issue->volume }}<br>No {{ $issue->number }}</span></div>
            <div>
              <p class="meta">Volume {{ $issue->volume }} &middot; Issue {{ $issue->number }} &middot; {{ $issue->year }}</p>
              @if ($issue->pages)
              <p class="meta">Page Nos. {{ $issue->pages }}</p>
              @endif
              <p class="meta">Online since {{ \Carbon\Carbon::parse($issue->created_at)->toFormattedDateString() }}</p>
            </div>
          </div>
        </article>
      </div>
    </section>

    @if (!$has_articles)
    <section class="section section-alt">
      <div class="container">
        <p class="meta">No articles have been published in this issue yet.</p>
      </div>
    </section>
    @endif

    @foreach ($article_groups as $heading => $group_articles)
    @if (count($group_articles))
    @php $band++; @endphp
    <!-- {{ strtoupper($heading) }} -->
    <section class="section{{ $band % 2 === 1 ? ' section-alt' : '' }}">
      <div class="container">
        <div class="section-head">
          <h2>{{ $heading }}</h2>
        </div>
        <ol class="article-list">
          @foreach ($group_articles as $article)
          <li class="article card">
            <div class="article-body">
              <h3><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h3>
              <p class="authors">{{ $article->authors }}</p>
              @if ($article->pages)
              <p class="meta">pp {{ $article->pages }}</p>
              @endif
            </div>
            <span class="views" title="Views"><span class="material-icons">visibility</span>{{ number_format($article->views) }}</span>
          </li>
          @endforeach
        </ol>
      </div>
    </section>
    @endif
    @endforeach

  </main>

@endsection
