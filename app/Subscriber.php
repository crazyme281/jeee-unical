<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $guarded = ['id'];

    use CrudTrait;
}
