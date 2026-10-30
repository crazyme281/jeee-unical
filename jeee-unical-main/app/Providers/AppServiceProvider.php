<?php

namespace App\Providers;

use App\Article;
use App\File;
use App\Manuscript;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Schema::defaultStringLength(191);

        Article::saving(function($article) {
            if(!$article->authors)
                $article->authors = '';
        });

        Manuscript::creating(function($manuscript) {
            if(!$manuscript->user_id)
                $manuscript->user_id = Auth::id();
        });

        Manuscript::deleting(function($manuscript) {
            // Delete the primary file
            $file_id = $manuscript->primary_file;
            $file = File::find($file_id);
            if($file)
                $file->delete();

            // Delete Supporting files
            $file_ids = $manuscript->supporting_files;
            if ($file_ids) {
                foreach ($file_ids as $key => $file_id) {
                    $file = File::find($file_id);
                    if($file)
                        $file->delete();
                }
            }
        });

        File::deleting(function($file) {
            \Storage::delete($file->link);
        });

//        Manuscript::saving(function($m){dd($m);});

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
