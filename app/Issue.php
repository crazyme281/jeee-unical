<?php
$page_title = 'Issues | JETSI';
$current_nav = 'issues';

// Retrieve published issues from the existing Laravel model.
$issues = \App\Issue::latest()->get();
?>

<div class="page-head">
    <div class="container">
        <h1>Issues</h1>
    </div>
</div>

<main id="main">
    <section class="section">
        <div class="container">

            @if ($issues->isEmpty())
                <p class="meta">No issues have been published yet.</p>
            @else
                <div class="issue-grid">
                    @foreach ($issues as $issue)
                        <article class="issue card">
                            <div class="cover" aria-hidden="true">
                                <span>
                                    Vol {{ e($issue->volume ?? '') }}<br>
                                    No {{ e($issue->number ?? '') }}
                                </span>
                            </div>

                            <div>
                                <h3>
                                    <a href="{{ url('issue/' . $issue->slug) }}">
                                        {{ e($issue->title) }}
                                    </a>
                                </h3>

                                <p class="meta">
                                    Volume {{ e($issue->volume ?? '') }}
                                    &middot;
                                    Issue {{ e($issue->number ?? '') }}
                                </p>

                                <p class="meta">
                                    Page Nos. {{ e($issue->pages ?? 'N/A') }}
                                </p>

                                <p class="meta">
                                    Online since
                                    {{ $issue->created_at
                                        ? $issue->created_at->format('M j, Y')
                                        : 'N/A' }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        </div>
    </section>
</main>