<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'code' => 'required|string|max:255|min:3|unique:products,code',
            'currency_id' => 'required|numeric|min:0',
            'unit_id' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'default_buy_price' => 'required|',
            'default_sell_price' => 'required|',
        ];
    }
}
