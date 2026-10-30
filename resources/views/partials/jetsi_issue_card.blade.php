{{-- Issue card used by the redesigned pages (home, issues list). Expects $issue. --}}
<article class="issue card">
  <div class="cover" aria-hidden="true"><span>Vol {{ $issue->volume }}<br>No {{ $issue->number }}</span></div>
  <div>
    <h3><a href="{{ route('issues.show', $issue->slug) }}">{{ $issue->title }}</a></h3>
    <p class="meta">Volume {{ $issue->volume }} &middot; Issue {{ $issue->number }}</p>
    @if ($issue->pages)
    <p class="meta">Page Nos. {{ $issue->pages }}</p>
    @endif
    <p class="meta">Online since {{ \Carbon\Carbon::parse($issue->created_at)->toFormattedDateString() }}</p>
  </div>
</article>
