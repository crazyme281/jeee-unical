<?php

namespace App;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $guarded = ['id'];

    use CrudTrait;

    public function manuscript() {
        return $this->belongsTo(Manuscript::class);
    }
}
