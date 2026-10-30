@extends('layouts.jetsi')

@section('title', 'Article')
@section('nav', 'issues')

@section('content')

@php
  // Keywords are stored comma-separated; trim them and drop empty ones.
  $keywords = array_values(array_filter(array_map('trim', explode(',', $article->keywords)), 'strlen'));
@endphp

  <div class="page-head">
    <div class="container"><h1>{{ $article->title }}</h1></div>
  </div>

  <main id="main">

    <!-- ARTICLE -->
    <section class="section">
      <div class="container">
        <div class="doc">
          <p class="eyebrow">{{ $article->type }}</p>
          <p class="lead">{{ $article->authors }}</p>
          <p class="meta">
            <a href="{{ route('issues.show', $article->issue->slug) }}">Volume {{ $article->issue->volume }}, Issue {{ $article->issue->number }}, {{ $article->issue->year }}</a>
            @if ($article->pages)&middot; Pages {{ $article->pages }}@endif
          </p>
          <p class="meta">Available online {{ \Carbon\Carbon::parse($article->created_at)->toFormattedDateString() }}</p>
          <p style="margin-top:16px">
            <a class="btn btn-primary" href="https://jomaarducth.org/storage/app/public/{{ $article->file }}"><span class="material-icons">file_download</span> Download article</a>
          </p>

          <section style="margin-top:40px">
            <h2>Abstract</h2>
            <div class="abstract">
              {!! html_entity_decode($article->abstract) !!}
            </div>
          </section>

          @if (count($keywords))
          <section>
            <h2>Keywords</h2>
            {{-- Kept on one line so no stray space appears before the commas. --}}
            <p>@foreach ($keywords as $keyword){{ $loop->first ? '' : ', ' }}<a href="{{ route('search.keyword', $keyword) }}">{{ $keyword }}</a>@endforeach</p>
          </section>
          @endif
        </div>
      </div>
    </section>

    <!-- PUBLISHED IN -->
    <section class="section section-alt">
      <div class="container">
        <div class="section-head">
          <h2>Published in</h2>
          <a class="link-arrow" href="{{ route('issues.show', $article->issue->slug) }}">View issue <span class="material-icons">arrow_forward</span></a>
        </div>
        <article class="current-issue card">
          <div class="current-body">
            <div class="cover cover-large" aria-hidden="true"><span>Vol {{ $article->issue->volume }}<br>No {{ $article->issue->number }}</span></div>
            <div>
              <h3><a href="{{ route('issues.show', $article->issue->slug) }}">{{ $article->issue->title }}</a></h3>
              <p class="meta">Volume {{ $article->issue->volume }} &middot; Issue {{ $article->issue->number }}</p>
              @if ($article->issue->pages)
              <p class="meta">Page Nos. {{ $article->issue->pages }}</p>
              @endif
              <p class="meta">Online since {{ \Carbon\Carbon::parse($article->issue->created_at)->toFormattedDateString() }}</p>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- RELATED ARTICLES -->
    @if (count($related_articles))
    <section class="section">
      <div class="container">
        <div class="section-head">
          <h2>Related articles</h2>
        </div>
        <ol class="article-list">
          @foreach ($related_articles as $related)
          <li class="article card">
            <div class="article-body">
              <h3><a href="{{ route('articles.show', $related->slug) }}">{{ $related->title }}</a></h3>
              <p class="authors">{{ $related->authors }}</p>
            </div>
          </li>
          @endforeach
        </ol>
      </div>
    </section>
    @endif

  </main>

@endsection
