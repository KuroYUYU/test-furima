<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'image'            => ['required', 'image', 'mimes:jpeg,png'],
            'category_ids'     => ['required', 'array'],
            'category_ids.*'   => ['integer', 'exists:categories,id'],
            'condition'        => ['required'],
            'name'             => ['required'],
            'brand_name'       => ['nullable'],
            'description'      => ['required', 'max:255'],
            'price'            => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'image.required'        => '画像を登録してください',
            'image.mimes'           => '「.png」または「.jpeg」形式でアップロードしてください',
            'category_ids.required' => 'カテゴリーを一つ以上選択してください',
            'condition.required'    => '商品の状態を選択してください',
            'name.required'         => '商品名を入力してください',
            'description.required'  => '商品説明を入力してください',
            'description.max'       => '商品説明は255文字以内で入力してください',
            'price.required'        => '値段を入力してください',
            'price.required'        => '商品価格を入力してください',
            'price.numeric'         => '商品価格は数値で入力してください',
            'price.min'             => '商品価格は0円以上で入力してください',
        ];
    }
}
