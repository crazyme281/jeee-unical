<?php

namespace App;

use Backpack\CRUD\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Article extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;
    use SearchableTrait;

    protected $guarded = ['id'];

    public function issue() {
        return $this->belongsTo(Issue::class);
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['issue.title', 'pages']
            ]
        ];
    }

    protected $searchable = [
        'columns' => [
            'articles.title' => 10,
            'articles.authors' => 10,
            'articles.keywords' => 10,
            'articles.abstract' => 10,
//            'articles.doi' => 5,
            'issues.title' => 5,
            'issues.volume' => 5,
            'issues.year' => 2,
        ],
        'joins' => [
            'issues' => ['articles.issue_id','issues.id'],
        ],
    ];

    public function scopePopular($query) {
        return $query->orderBy('articles.views', 'desc');
    }

    public function setFileAttribute($value)
    {
        $attribute_name = "file";
        $disk = "public";
        $destination_path = "articles";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
