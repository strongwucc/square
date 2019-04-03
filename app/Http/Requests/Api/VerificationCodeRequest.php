<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
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
            'mobile' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',
                'unique:o2o_member'
            ]
        ];
    }

    public function attributes()
    {
        return [
            'mobile' => '手机号'
        ];
    }

    public function messages()
    {
        return [
            'mobile.regex' => '请输入正确的手机号',
            'mobile.unique' => '该手机号已被绑定',
            'mobile.required' => '手机号不能为空',
        ];
    }
}
