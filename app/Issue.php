<?php

namespace App;

use Backpack\CRUD\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;

    protected $guarded = ['id'];

    public function articles() {
        return $this->hasMany(Article::class);
    }

    public function scopeCurrent($query) {
        return $query->where('issues.status', 'Current')->orderBy('issues.created_at', 'desc');
    }

    public function scopeLatest($query) {
        return $query->where('status', 'Published')->orderBy('issues.year', 'desc')->orderBy('number', 'desc');
    }

    /**
     * Every issue that should appear in public listings (Current or Published),
     * newest first. `number` is stored as text, so it is cast to an integer
     * to make sure "10" sorts after "2".
     */
    public function scopeListed($query) {
        return $query->whereIn('issues.status', ['Current', 'Published'])
                     ->orderBy('issues.year', 'desc')
                     ->orderByRaw('CAST(issues.number AS integer) desc');
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['title']
            ]
        ];
    }

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "issues";

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());
            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $destination_path.'/'.$filename;
        }
    }
}