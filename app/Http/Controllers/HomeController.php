<?php

namespace App\Http\Controllers;

use App\Article;
use App\Issue;
use App\Services\Paystack\Plan;
use App\Services\Paystack\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('subscribe');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Current Issue
        $current_issue = Issue::current()->first();
        // Get Latest Issues (Current or Published)
        $recent_issues = Issue::listed()->take(4)->get();
        // Get Popular Articles
        $popular_articles = Article::popular()->with('issue')->take(4)->get();

        return view('home', ['issues' => $recent_issues, 'articles' => $popular_articles, 'current_issue' => $current_issue]);
    }

    public function guideline()
    {
        $current_issue = Issue::current()->first();
//        return $current_issue;
        return view('guideline')->with('current_issue', $current_issue);
    }

    public function subscribe() {
        return redirect('subscription');
    }

    public function subscription()
    {
        $volumes = Issue::listed()->get()->unique('volume');
        return view('subscription')->with(['volumes' => $volumes]);
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }
}