<?php

namespace App\Http\Controllers;

use App\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    private $issue;

    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    public function index()
    {
        $issues = $this->issue->latest()->get();
        return view('issues.index', ['issues' => $issues]);
    }

    public function show($slug)
    {
        $issue = $this->issue->findBySlugOrFail($slug);

        $original_articles = $issue->articles()->where('type', 'Original')->get();
        $review_articles = $issue->articles()->where('type', 'Review')->get();
        $report_articles = $issue->articles()->where('type', 'Report')->get();
        $commentary_articles = $issue->articles()->where('type', 'Commentary')->get();
        $clinicalpractise_articles = $issue->articles()->where('type', 'Clinical Practice')->get();
        $letter_to_editor_articles = $issue->articles()->where('type', 'Letter to Editor')->get();

        return view('issues.show', [
            'issue' => $issue,
            'original_articles' => $original_articles,
            'review_articles' => $review_articles,
            'report_articles' => $report_articles,
            'commentary_articles' => $commentary_articles,
            'clinicalpractise_articles' => $clinicalpractise_articles,
            'letter_to_editor_articles' => $letter_to_editor_articles,
        ]);
    }
}