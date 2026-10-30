@extends('layouts.base')

@section('title', 'Search')
@section('issues.active', 'active')

@section('content')
    <section class="">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="title">
                        <h3>
                            Search Results
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h4 class="title">
                Articles
            </h4>
            <hr>
            <div class="row articles">
                @each('partials.articles_listing', $articles, 'article')
            </div>
        </div>
    </section>
@endsection