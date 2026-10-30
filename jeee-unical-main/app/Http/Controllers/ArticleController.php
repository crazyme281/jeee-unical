<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    private $article;
    public function __construct(Article $article)
    {
        $this->article = $article;
    }


    public function index()
    {
        $articles = $this->article->get();
        return view('articles.index', ['articles' => $articles]);
    }

    public function show($slug)
    {
        $article = $this->article->findBySlug($slug);
        $related_articles = Article::where('issue_id', $article->issue_id)->where('id', '<>', $article->id)->take(4)->get();

        // Count views
        $article->increment('views');

        return view('articles.show', [
            'article' => $article,
            'related_articles' => $related_articles
        ]);
    }
}
