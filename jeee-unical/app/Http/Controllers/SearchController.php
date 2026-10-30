<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function index(Request $request)
    {
        $query = "";
        foreach ($request->all() as $item){
            if($item)
                $query .= " {$item}";
        }

        if(empty($query)){
            return redirect()->route('home');
        }

        $articles = $this->article->search($query)->get();
        return view('search.index')->with(['articles' => $articles]);
    }

    public function keyword($keyword)
    {
        if(empty($keyword)){
            return redirect()->route('home');
        }

        $articles = $this->article->search($keyword)->get();
        return view('search.index')->with(['articles' => $articles]);
    }

}
