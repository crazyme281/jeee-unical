@extends('layouts.base')

@section('title', 'My Profile')
@section('manuscript.active', 'active')

@section('content')
    <section class="">
        <div class="container">
            <div class="col-md-12">
                <div class="title">
                    <h3>
                        My Manuscripts
                    </h3>
                </div>
            </div>

            <div class="col-xs-8 col-sm-4">
                <a href="{{ route('manuscripts.create') }}" class="btn btn-primary btn-md">
                    <i class="material-icons">unarchive</i> Submit a Manuscript
                    <div class="ripple-container"></div></a>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Title</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @if($manuscripts->count() > 0)
                        @foreach($manuscripts as $manuscript)
                            <tr>
                                <td class="text-center">1</td>
                                <td>{{ $manuscript['title'] }}</td>
                                <td>{{ $manuscript['created_at'] }}</td>
                                <td>
                                    <span class="label label-{{ $manuscript->StatusClass }}">{{ $manuscript['status'] }}</span>
                                </td>
                                <td class="td-actions text-right">
                                    <a href="{{ route('manuscripts.edit', $manuscript['id']) }}" rel="tooltip" title="" class="btn btn-success btn-simple btn-xs" data-original-title="Edit Manuscript" {{ in_array($manuscript['status'], ['Review', 'Revision']) ? '' : 'disabled' }}>
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td><h5>No records</h5></td></tr>
                    @endif

                </tbody>
            </table>
        </div>
    </section>
@endsection