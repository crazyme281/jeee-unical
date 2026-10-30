@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}<small>Overview of the journal</small>
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
    $stats = [
        ['Issues',      $counts['issues'],      'book',          'magenta', $admin.'/issue'],
        ['Articles',    $counts['articles'],    'file-text-o',   'blue',    $admin.'/article'],
        ['Manuscripts', $counts['manuscripts'], 'files-o',       'teal',    $admin.'/manuscript'],
        ['Subscribers', $counts['subscribers'], 'envelope-o',    'amber',   $admin.'/subscriber'],
        ['Users',       $counts['users'],       'users',         'slate',   $admin.'/user'],
    ];
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

    <div class="jx-stats">
        @foreach ($stats as $stat)
        <a class="jx-stat" href="{{ url($stat[4]) }}">
            <span class="jx-stat-icon jx-{{ $stat[3] }}"><i class="fa fa-{{ $stat[2] }}"></i></span>
            <span>
                <span class="jx-stat-num">{{ number_format($stat[1]) }}</span>
                <span class="jx-stat-label">{{ $stat[0] }}</span>
            </span>
        </a>
        @endforeach
    </div>

    <div class="jx-grid-2">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Manuscripts by status</h3>
            </div>
            <div class="box-body jx-status-list">
                @foreach ($statuses as $status => $total)
                <a class="jx-status-row" href="{{ url($admin.'/manuscript') }}">
                    <span class="jx-chip jx-chip-{{ strtolower($status) }}">{{ $status }}</span>
                    <strong>{{ $total }}</strong>
                </a>
                @endforeach
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Recent submissions</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-default btn-xs" href="{{ url($admin.'/manuscript') }}">View all</a>
                </div>
            </div>
            <div class="box-body no-padding">
                @if ($recent_manuscripts->isEmpty())
                    <p class="jx-empty">No manuscripts have been submitted yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover jx-table">
                        <thead>
                            <tr><th>Title</th><th>Submitted by</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($recent_manuscripts as $manuscript)
                            <tr>
                                <td><a href="{{ url($admin.'/manuscript/'.$manuscript->id.'/edit') }}">{{ str_limit($manuscript->title, 60) }}</a></td>
                                <td>{{ $manuscript->user ? trim($manuscript->user->first_name.' '.$manuscript->user->last_name) : '—' }}</td>
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
    </div>
@endsection