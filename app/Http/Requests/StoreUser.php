<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|unique:users,name',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required',
            'phone'         => 'required|min:10|max:15',
            'permission'    => 'required|numeric'
        ];
    }
}
