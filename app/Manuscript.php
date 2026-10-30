<?php

namespace App;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Manuscript extends Model
{
    use CrudTrait;

    protected $guarded = ['id', 'clear_support_files'];

    protected $casts = [
        'authors' => 'array',
        'supporting_files' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function authors() {
        return $this->hasMany(Author::class);
    }

    public function editor() {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getStatusClassAttribute() {
        switch ($this->attributes['status']) {
            case 'Draft':
                return 'default';
            case 'Review':
                return 'info';
            case 'Revision':
                return 'warning';
            case 'Approved':
                return 'success';
            case 'Rejected':
                return 'warning';
            default:
                return 'default';
        }
    }

    /*
     * mutator for Admin Crud
     * Do not Edit
     * */

    public function getFileAttribute() {
        $id = $this->getAttribute('primary_file');
        $file = File::find($id);
        if(!$file)
            return null;
        return $file->link;
    }

    public function getSupportFilesAttribute() {
        $ids = $this->getAttribute('supporting_files');
        if(!$ids)
            return null;
        $files = File::query()->whereIn('id', $ids)->get();
        if($files->isEmpty())
            return null;
        $data = [];
        foreach ($files as $file) {
            $data[] = [
                'path' => $file->link,
                'description' => $file->getAttribute('description')
            ];
        }
        return $data;
    }

    public function setFileAttribute($value) {
        $attribute_name = "file";
        $this->uploadFileToDisk($value, $attribute_name);
    }

    public function setSupportFilesAttribute($value) {
        $attribute_name = "support_files";
        $this->uploadMultipleFilesToDisk($attribute_name);
    }

    public function uploadFileToDisk($value, $attribute_name)
    {
        $request = \Request::instance();

        $attr = 'primary_file';

        // if a new file is uploaded, delete the file from the disk
        if ($request->hasFile($attribute_name) &&
            $this->{$attribute_name} &&
            $this->{$attribute_name} != null) {

            // Delete the file record
            $file = File::find($this->attributes[$attr]);
            if($file)
                $file->delete();

            $this->attributes[$attr] = null;
        }

        // if the file input is empty, delete the file from the disk
        if (is_null($value) && $this->{$attribute_name} != null) {
            // Delete the file record
            $file = File::find($this->attributes[$attr]);
            if($file)
                $file->delete();

            $this->attributes[$attr] = null;
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if ($request->hasFile($attribute_name) && $request->file($attribute_name)->isValid()) {
            $path = '/submissions';

            //Handle the primary file
            $file = $request->file($attribute_name);
            $name = str_random(20);
            $original_name = $file->getClientOriginalName();
            $ex = $file->extension();
            $full_name = $name.'.'.$ex;
            // save primary file
            \Storage::put("{$path}/{$full_name}", file_get_contents($file->getRealPath()));

            $stored_primary_file = File::create([
                'name' => $original_name,
                'link' => "{$path}/{$full_name}",
                'size' => $file->getClientSize(),
                'type' => 'docx'
            ]);

            $this->attributes[$attr] = $stored_primary_file->id;
        }
    }

    public function uploadMultipleFilesToDisk($attribute_name)
    {
        $request = \Request::instance();

        $previous_files = (array) $this->{$attribute_name};

        $attribute_value = [];

        foreach ($previous_files as $f) {
            $attribute_value[] = $f['path'];
        }

        $files_to_clear = $request->get('clear_'.$attribute_name);

        // if a file has been marked for removal,
        // delete it from the disk and from the db
        if ($files_to_clear) {
            foreach ($files_to_clear as $key => $filename) {
                // Delete associated file
                $file = File::where('link', $filename)->first();
                if($file)
                    $file->delete();

                $attribute_value = array_where($attribute_value, function ($value) use ($filename) {
                    return $value != $filename;
                });
            }
        }

        $path = '/submissions';
        // if a new file is uploaded, store it on disk and its filename in the database
        if($request->has($attribute_name)){
            foreach ($request[$attribute_name] as $file){
                // If immediate file
                if (!$request->hasFile($attribute_name)) {
                    // Check if file is wrapped and with description
                    if(!isset($file['file']) || empty($file['description'])){
                        continue;
                    }
                    $description = $file['description'];
                    $file = $file['file'];
                }

                if ($file->isValid()) {
                    $name = str_random(20);
                    $original_name = $file->getClientOriginalName();
                    $ex = $file->extension();
                    $full_name = $name.'.'.$ex;
                    // save primary file
                    \Storage::put("{$path}/{$full_name}", file_get_contents($file->getRealPath()));

                    File::create([
                        'name' => $original_name,
                        'description' => $description ?? $original_name,
                        'link' => "{$path}/{$full_name}",
                        'size' => $file->getClientSize(),
                        'type' => 'docx'
                    ]);

                    // 3. Add the public path to the database
                    $attribute_value[] = "{$path}/{$full_name}";
                }
            }
        }

        if ($request->hasFile($attribute_name)) {
            foreach ($request->file($attribute_name) as $file) {

            }
        }

        $files = File::query()->whereIn('link', $attribute_value)->pluck('id');

        $this->setAttribute('supporting_files', $files->toArray());
    }

    /*End Admin Crud Files*/
}