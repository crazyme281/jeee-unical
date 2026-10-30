@extends('layouts.jetsi')

@section('title', 'Issues')
@section('nav', 'issues')

@section('content')

  <div class="page-head">
    <div class="container"><h1>Issues</h1></div>
  </div>

  <main id="main">
    <section class="section">
      <div class="container">
        @if (!count($issues))
        <p class="meta">No issues have been published yet.</p>
        @else
        <div class="issue-grid">
          @foreach ($issues as $issue)
            @include('partials.jetsi_issue_card', ['issue' => $issue])
          @endforeach
        </div>
        @endif
      </div>
    </section>
  </main>

@endsection
