@if (Auth::check())
@php
    $admin = config('backpack.base.route_prefix', 'admin');
    $display_name = trim(Auth::user()->first_name.' '.Auth::user()->last_name);
    if ($display_name === '') { $display_name = Auth::user()->email; }
@endphp
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
            <span class="jx-avatar">{{ mb_strtoupper(mb_substr($display_name, 0, 1)) }}</span>
          </div>
          <div class="pull-left info">
            <p>{{ $display_name }}</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Administrator</a>
          </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          <li class="header">Overview</li>
          <li><a href="{{ url($admin.'/dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

          <li class="header">Journal</li>
          <li><a href="{{ url($admin.'/issue') }}"><i class="fa fa-book"></i> <span>Issues</span></a></li>
          <li><a href="{{ url($admin.'/article') }}"><i class="fa fa-file-text-o"></i> <span>Articles</span></a></li>

          <li class="header">Submissions</li>
          <li><a href="{{ url($admin.'/manuscript') }}"><i class="fa fa-files-o"></i> <span>Manuscripts</span></a></li>
          <li><a href="{{ url($admin.'/author') }}"><i class="fa fa-pencil-square-o"></i> <span>Authors</span></a></li>

          <li class="header">People</li>
          <li><a href="{{ url($admin.'/user') }}"><i class="fa fa-users"></i> <span>Users</span></a></li>
          <li><a href="{{ url($admin.'/subscriber') }}"><i class="fa fa-envelope-o"></i> <span>Subscribers</span></a></li>

          <li class="header">Site</li>
          <li><a href="{{ url($admin.'/setting') }}"><i class="fa fa-cog"></i> <span>Settings</span></a></li>
          <li><a href="{{ url('/') }}" target="_blank"><i class="fa fa-external-link"></i> <span>View website</span></a></li>
          <li><a href="{{ url($admin.'/logout') }}"><i class="fa fa-sign-out"></i> <span>{{ trans('backpack::base.logout') }}</span></a></li>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
@endif