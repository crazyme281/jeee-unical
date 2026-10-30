<div class="col-md-6">
    <div class="row article" data-toggle="tooltip" data-placement="top" title="{{ $article['title'] }}">
        <div class="col-xs-2 article-icon">
            <i class="material-icons dp48 img-circle img-raised">library_books</i>
        </div>
        <div class="col-xs-10">
            <h6 class="title article-title">
                <a href="{{ route('articles.show', $article['slug']) }}">{{ $article['title'] }}</a>
            </h6>
            <p class="authors">
                <span>{{ $article['authors'] }}</span>
            </p>
            <p>Vol. {{ $article->issue['volume'] }} | Issue {{ $article->issue['number'] }}, {{ $article->issue['year'] }} | pp {{ $article['pages'] }} | views <span class="label label-default">{{ $article['views'] }}</span></p>
        </div>

    </div>
</div>