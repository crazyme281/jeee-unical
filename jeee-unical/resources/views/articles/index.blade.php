@extends('layouts.jetsi')

@section('title', 'Articles')
@section('nav', 'issues')

@section('content')

  <div class="page-head">
    <div class="container"><h1>Articles</h1></div>
  </div>

  <main id="main">
    <section class="section">
      <div class="container">

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

        <div class="section-head" style="margin-top:40px">
          <h2>All articles</h2>
        </div>

        @if (!count($articles))
        <p>No articles have been published yet.</p>
        @else
        <ol class="article-list">
          @foreach ($articles as $article)
          <li class="article card">
            <div class="article-body">
              <h3><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h3>
              <p class="authors">{{ $article->authors }}</p>
              <p class="meta">Vol. {{ $article->issue->volume }} &middot; Issue {{ $article->issue->number }}, {{ $article->issue->year }}@if ($article->pages) &middot; pp {{ $article->pages }}@endif</p>
            </div>
            <span class="views" title="Views"><span class="material-icons">visibility</span>{{ number_format($article->views) }}</span>
          </li>
          @endforeach
        </ol>
        @endif

      </div>
    </section>
  </main>

@endsection
