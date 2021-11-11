<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LiqpayRequest extends FormRequest
{
    public function authorize()
    {
        return TRUE;
    }

    public function rules()
    {
        return [
            'signature' => 'required',
            'data'      => 'required',
        ];
    }
}
