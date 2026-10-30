<?php

namespace App\Http\Controllers;

use App\Article;
use App\Services\Paystack\Plan;
use App\Services\Paystack\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function article($id)
    {
        $article = Article::findBySlug($id)->first();
        $issue= $article->issue;

        if(!Auth::user()->subscribedToIssue($issue->id)){
            $status = [
                'type' => 'warning',
                'message' => 'Please subscribe to access this content.',
            ];
            return redirect()->route('articles.show', $id)->with('status', $status);
        }

        return response()->download("storage/{$article->file}");
    }
}
