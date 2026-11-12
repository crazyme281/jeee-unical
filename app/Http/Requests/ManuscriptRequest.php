<?php

namespace App\Http\Requests;

class ManuscriptRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
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
        $file = request()->hasFile('file') ? 'file|mimes:doc,docx,pdf|max:215040' : 'nullable|string';
        return [
            'title' => 'required|string|min:10|max:200',
            'abstract' => 'required|string|min:50|max:5000',
            'keywords' => 'required|string|min:3|max:50',
            'file' => $file,
            'support_files.*' => request()->hasFile('support_files') ? 'file|mimes:doc,docx,pdf,jpg,jpeg,png,xlsx,xls|max:215040' : 'nullable',
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
