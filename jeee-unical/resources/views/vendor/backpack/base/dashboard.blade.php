@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}<small>Journal activity at a glance</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')
@php
    $admin = config('backpack.base.route_prefix', 'admin');
    $first = Auth::user()->first_name ?: 'there';
    $ms = url($admin.'/manuscript');
    $stats = [
        ['Total Manuscripts',    $cards['total'],     'files-o',      'teal',    $ms],
        ['New Submissions',      $cards['submitted'], 'inbox',        'blue',    $ms],
        ['Under Review',         $cards['review'],    'search',       'amber',   $ms],
        ['Awaiting Revision',    $cards['revision'],  'pencil',       'orange',  $ms],
        ['Accepted Manuscripts', $cards['approved'],  'check-circle', 'green',   $ms],
        ['Rejected Manuscripts', $cards['rejected'],  'times-circle', 'red',     $ms],
        ['Published Articles',   $cards['published'], 'file-text-o',  'magenta', url($admin.'/article')],
        ['Registered Authors',   $cards['authors'],   'users',        'indigo',  url($admin.'/user')],
        ['Active Reviewers',     $cards['reviewers'], 'user-circle-o','slate',   url($admin.'/user')],
    ];
    $today = \Carbon\Carbon::today();
    $person = function ($u) { return $u ? trim($u->first_name.' '.$u->last_name) : '—'; };
@endphp

    <div class="jx-welcome">
        <div>
            <h2>Welcome back, {{ $first }}</h2>
            <p>Manage issues, articles and incoming manuscripts for JETSI.</p>
        </div>
        <div class="jx-welcome-actions">
            <a class="btn jx-btn-light" href="{{ url($admin.'/issue/create') }}"><i class="fa fa-plus"></i> New issue</a>
            <a class="btn jx-btn-light" href="{{ url($admin.'/article/create') }}"><i class="fa fa-plus"></i> New article</a>
        </div>
    </div>

    @unless ($tracking)
        <div class="alert alert-warning">
            Editor, reviewer and deadline tracking is not set up yet. Run <code>php artisan migrate</code> to enable the
            "Active Reviewers", "Awaiting Editor Assignment", "Pending Reviewer Reports" and "Upcoming Editorial Deadlines" sections.
        </div>
    @endunless

    {{-- Summary cards --}}
    <div class="jx-stats jx-stats-9">
        @foreach ($stats as $stat)
        <a class="jx-stat" href="{{ $stat[4] }}">
            <span class="jx-stat-icon jx-{{ $stat[3] }}"><i class="fa fa-{{ $stat[2] }}"></i></span>
            <span>
                <span class="jx-stat-num">{{ number_format($stat[1]) }}</span>
                <span class="jx-stat-label">{{ $stat[0] }}</span>
            </span>
        </a>
        @endforeach
    </div>

    {{-- Charts: submissions + status --}}
    <div class="jx-grid-chart">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Monthly manuscript submissions</h3>
                <div class="box-tools pull-right"><span class="text-muted small">Last 12 months</span></div>
            </div>
            <div class="box-body">
                @if (array_sum($charts['monthly']['data']) === 0)
                    <p class="jx-empty">No manuscripts have been submitted in the last 12 months.</p>
                @else
                    <div class="jx-chart"><canvas id="chartMonthly" aria-label="Monthly manuscript submissions" role="img"></canvas></div>
                @endif
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Manuscript status distribution</h3>
            </div>
            <div class="box-body">
                @if (array_sum($charts['status']['data']) === 0)
                    <p class="jx-empty">No manuscripts yet.</p>
                @else
                    <div class="jx-chart"><canvas id="chartStatus" aria-label="Manuscript status distribution" role="img"></canvas></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Publication statistics + recent publications --}}
    <div class="jx-grid-chart">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Publication statistics</h3>
                <div class="box-tools pull-right"><span class="text-muted small">By issue year</span></div>
            </div>
            <div class="box-body">
                @if (empty($charts['publications']['labels']))
                    <p class="jx-empty">No articles have been published yet.</p>
                @else
                    <div class="jx-chart"><canvas id="chartPublications" aria-label="Articles published and downloads by year" role="img"></canvas></div>
                    <div class="jx-mini-stats">
                        <span><strong>{{ number_format($cards['published']) }}</strong> articles</span>
                        <span><strong>{{ number_format($pub_totals['issues']) }}</strong> issues</span>
                        <span><strong>{{ number_format($pub_totals['views']) }}</strong> views</span>
                        <span><strong>{{ number_format($pub_totals['downloads']) }}</strong> downloads</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Recent publications</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-default btn-xs" href="{{ url($admin.'/article') }}">View all</a>
                </div>
            </div>
            <div class="box-body no-padding">
                @if ($recent_articles->isEmpty())
                    <p class="jx-empty">No articles have been published yet.</p>
                @else
                    <ul class="jx-list">
                    @foreach ($recent_articles as $article)
                        <li>
                            <a href="{{ url($admin.'/article/'.$article->id.'/edit') }}">{{ str_limit($article->title, 70) }}</a>
                            <span class="text-muted small">
                                {{ $article->issue ? 'Vol. '.$article->issue->volume.', No. '.$article->issue->number.' ('.$article->issue->year.')' : 'No issue' }}
                                &middot; {{ $article->created_at ? $article->created_at->format('j M Y') : '' }}
                            </span>
                        </li>
                    @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent submissions + awaiting editor --}}
    <div class="jx-grid-eq">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Recent manuscript submissions</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-default btn-xs" href="{{ $ms }}">View all</a>
                </div>
            </div>
            <div class="box-body no-padding">
                @if ($recent_manuscripts->isEmpty())
                    <p class="jx-empty">No manuscripts have been submitted yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover jx-table">
                        <thead>
                            <tr><th>Title</th><th>Author</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($recent_manuscripts as $manuscript)
                            <tr>
                                <td><a href="{{ url($admin.'/manuscript/'.$manuscript->id.'/edit') }}">{{ str_limit($manuscript->title, 50) }}</a></td>
                                <td>{{ $person($manuscript->user) }}</td>
                                <td><span class="jx-chip jx-chip-{{ strtolower($manuscript->status) }}">{{ $manuscript->status }}</span></td>
                                <td class="text-muted">{{ $manuscript->created_at ? $manuscript->created_at->format('j M Y') : '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Manuscripts awaiting editor assignment</h3>
            </div>
            <div class="box-body no-padding">
                @if ($awaiting_editor->isEmpty())
                    <p class="jx-empty">Every active manuscript has an editor assigned.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover jx-table">
                        <thead>
                            <tr><th>Title</th><th>Author</th><th>Waiting</th><th></th></tr>
                        </thead>
                        <tbody>
                        @foreach ($awaiting_editor as $manuscript)
                            <tr>
                                <td><a href="{{ url($admin.'/manuscript/'.$manuscript->id.'/edit') }}">{{ str_limit($manuscript->title, 45) }}</a></td>
                                <td>{{ $person($manuscript->user) }}</td>
                                <td class="text-muted">{{ $manuscript->created_at ? $manuscript->created_at->diffForHumans(null, true) : '' }}</td>
                                <td class="text-right"><a class="btn btn-default btn-xs" href="{{ url($admin.'/manuscript/'.$manuscript->id.'/edit') }}">Assign</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Pending reports + deadlines --}}
    <div class="jx-grid-eq">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Pending reviewer reports</h3>
            </div>
            <div class="box-body no-padding">
                @if ($pending_reports->isEmpty())
                    <p class="jx-empty">No reviewer reports are outstanding.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover jx-table">
                        <thead>
                            <tr><th>Manuscript</th><th>Reviewer</th><th>Due</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($pending_reports as $manuscript)
                            @php $due = $manuscript->review_deadline ? \Carbon\Carbon::parse($manuscript->review_deadline) : null; @endphp
                            <tr>
                                <td><a href="{{ url($admin.'/manuscript/'.$manuscript->id.'/edit') }}">{{ str_limit($manuscript->title, 45) }}</a></td>
                                <td>{{ $person($manuscript->reviewer) }}</td>
                                <td>
                                    @if ($due)
                                        <span class="{{ $due->lt($today) ? 'jx-overdue' : 'text-muted' }}">{{ $due->format('j M Y') }}</span>
                                    @else
                                        <span class="text-muted">No date</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Upcoming editorial deadlines</h3>
            </div>
            <div class="box-body no-padding">
                @if ($deadlines->isEmpty())
                    <p class="jx-empty">No deadlines have been set on active manuscripts.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover jx-table">
                        <thead>
                            <tr><th>Manuscript</th><th>Status</th><th>Deadline</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($deadlines as $manuscript)
                            @php
                                $due = \Carbon\Carbon::parse($manuscript->review_deadline);
                                $days = $today->diffInDays($due, false);
                            @endphp
                            <tr>
                                <td><a href="{{ url($admin.'/manuscript/'.$manuscript->id.'/edit') }}">{{ str_limit($manuscript->title, 45) }}</a></td>
                                <td><span class="jx-chip jx-chip-{{ strtolower($manuscript->status) }}">{{ $manuscript->status }}</span></td>
                                <td>
                                    <span class="{{ $days < 0 ? 'jx-overdue' : 'text-muted' }}">{{ $due->format('j M Y') }}</span>
                                    <span class="small {{ $days < 0 ? 'jx-overdue' : 'text-muted' }}">
                                        {{ $days < 0 ? abs($days).' day'.(abs($days) == 1 ? '' : 's').' overdue' : ($days == 0 ? 'today' : 'in '.$days.' day'.($days == 1 ? '' : 's')) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Activity --}}
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Recent administrative activities</h3>
        </div>
        <div class="box-body no-padding">
            @if ($activities->isEmpty())
                <p class="jx-empty">Nothing has happened yet.</p>
            @else
                <ul class="jx-list jx-activity">
                @foreach ($activities as $activity)
                    <li>
                        <span class="jx-activity-icon"><i class="fa fa-{{ $activity['icon'] }}"></i></span>
                        <a href="{{ $activity['url'] }}">{{ $activity['text'] }}</a>
                        <span class="text-muted small" title="{{ $activity['at']->format('j M Y, H:i') }}">{{ $activity['at']->diffForHumans() }}</span>
                    </li>
                @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection

@section('after_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
    (function () {
        if (typeof Chart === 'undefined') { return; }

        var charts = {!! json_encode($charts, JSON_HEX_TAG | JSON_HEX_AMP) !!};
        var magenta = '#a9247f', blue = '#2f7bbf', teal = '#1b998b', amber = '#e0a100',
            orange = '#e8710a', green = '#1b8a5a', red = '#c0392b', slate = '#5d6475', grid = '#eef0f3';

        Chart.defaults.font.family = 'Roboto, Arial, sans-serif';
        Chart.defaults.color = '#5d6475';

        var el = document.getElementById('chartMonthly');
        if (el) {
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: charts.monthly.labels,
                    datasets: [{ label: 'Submissions', data: charts.monthly.data, backgroundColor: magenta, borderRadius: 4, maxBarThickness: 36 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid } }, x: { grid: { display: false } } }
                }
            });
        }

        el = document.getElementById('chartStatus');
        if (el) {
            new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: charts.status.labels,
                    datasets: [{ data: charts.status.data, backgroundColor: [slate, blue, amber, orange, green, red], borderWidth: 2, borderColor: '#fff' }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '62%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } }
                }
            });
        }

        el = document.getElementById('chartPublications');
        if (el) {
            new Chart(el, {
                data: {
                    labels: charts.publications.labels,
                    datasets: [
                        { type: 'bar', label: 'Articles published', data: charts.publications.articles, backgroundColor: blue, borderRadius: 4, maxBarThickness: 36, yAxisID: 'y' },
                        { type: 'line', label: 'Downloads', data: charts.publications.downloads, borderColor: magenta, backgroundColor: magenta, tension: .3, pointRadius: 3, yAxisID: 'y1' }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } },
                    scales: {
                        y:  { beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid }, title: { display: true, text: 'Articles' } },
                        y1: { beginAtZero: true, position: 'right', ticks: { precision: 0 }, grid: { drawOnChartArea: false }, title: { display: true, text: 'Downloads' } },
                        x:  { grid: { display: false } }
                    }
                }
            });
        }
    })();
    </script>
@endsection