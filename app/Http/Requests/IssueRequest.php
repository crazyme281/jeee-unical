<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class IssueRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
            'number' => 'required|integer',
            'title' => 'required|max:250',
            'volume' => 'required|integer|min:1',
            'year' => 'required|integer',
            'pages' => 'required',
            'status' => 'required|in:Current,Published',
            'image' => request()->hasFile('image') ? 'required|image|max:10240' : 'nullable|string'
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
