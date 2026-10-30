<div class="col-sm-6">
    <div class="row issue">
        <div class="col-xs-3">
            <a href="{{ route('issues.show', $issue['slug']) }}">
                <img src="{{ asset('img/medical_test_stethoscope-128.png') }}" alt="Raised Image" class="img-rounded img-responsive img-raised">
            </a>
        </div>
        <div class="col-xs-9">
            <h4 class="issue-title"><a href="{{ route('issues.show', $issue['slug']) }}">{{ $issue['title'] }}</a></h4>
            <p>Volume {{ $issue['volume'] }} | Issue {{ $issue['number'] }}</p>
            <p>Page Nos. {{ $issue['pages'] }}</p>
            <p>Online since {{ \Carbon\Carbon::parse($issue['created_at'])->toFormattedDateString() }}</p>
        </div>
    </div>
</div>