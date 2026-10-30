<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends \Backpack\CRUD\app\Http\Requests\CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return \Auth::check() && Auth::user()->admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ignore = 0;
        if($edit = $this->request->has('id')) { // updating
            $ignore = $this->user()->is_admin ? $this->request->get('id') : \Auth::id();
        }

        if($this->user()->is_admin) {
            if(\Auth::id() == $this->request->get('id')) // make sure user can not remove himself as admin
                $admin_rule = 'in:1';
            else
                $admin_rule = 'in:0,1';

        } else {
            $admin_rule = 'in:0';
        }

        return [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'email' => ['email', Rule::unique('users')->ignore($ignore)],
            'admin' => $admin_rule,
            'password' => $edit ? 'nullable|min:3' : 'required|min:3',

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
