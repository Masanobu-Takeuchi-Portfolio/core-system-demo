<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name_last' => ['required', 'string', 'max:20'],
            'name_first' => ['required', 'string', 'max:20'],
            //'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::guard('admin')->user()->email . ',email'],
            //'password' => ['required', 'max: 128', 'confirmed', Password::min(8)->uncompromised()], // uncompromised()は過去にデータ漏洩したことがあるかを確認(ninja,password等)
            'url_transportation_expenses' => ['url', 'nullable'],
            'url_schedule' => ['url', 'nullable']
        ];
    }
}
