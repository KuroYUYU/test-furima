<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image_path' => ['nullable', 'image', 'mimes:jpeg,png'],
            // 8文字ちょうど,123-4567 の形だけ許可
            'nickname'           => ['required', 'string', 'max:20'],
            'postcode'           => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address'            => ['required', 'string'],
            'building'           => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'profile_image_path.mimes'  => '「.png」または「.jpeg」形式でアップロードしてください',
            'nickname.required'         => 'ユーザー名を入力してください',
            'nickname.max'              => 'ユーザー名は20文字以内で入力してください',
            'postcode.required'         => '郵便番号を入力してください',
            'postcode.regex'            => '郵便番号はハイフンありの8文字で入力してください',
            'address.required'          => '住所を入力してください',
        ];
    }
}
