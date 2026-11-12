<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ArticleRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:250',
            'abstract' => 'required|string',
            'authors' => 'required|string',
            'keywords' => 'required|string',
            'issue_id' => 'required|exists:issues,id',
            'type' => 'required|string',
            'file' => request()->hasFile('file') ? 'required|file|mimes:pdf|max:215040' : 'nullable|string',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
