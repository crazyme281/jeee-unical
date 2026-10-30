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
        $file = 'mimes:doc,docx';
        if($this->request->has('file') && $this->request->get('file') == null) {
            $file .= "|required";
        }
        return [
            'title' => 'required|string|min:10|max:200',
            'abstract' => 'required|string|min:50|max:5000',
            'keywords' => 'required|string|min:3|max:50',
            'file' =>  $file
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
