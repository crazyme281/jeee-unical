<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
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

    public function sluggable(): array
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

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
            return;
        }

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
            $image = imagecreatefromstring(base64_decode(explode(',', $value, 2)[1], true));
            if ($image === false) {
                throw \Illuminate\Validation\ValidationException::withMessages(['image' => 'Choose a valid image.']);
            }
            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';
            // 2. Store the image on disk.
            ob_start();
            imagejpeg($image, null, 90);
            $contents = ob_get_clean();
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $contents);
            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $destination_path.'/'.$filename;
        }
    }
}
