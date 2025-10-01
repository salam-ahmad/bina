<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:cash,loan',
            'total' => 'required|numeric|min:1',
            'note' => 'nullable|string',
//            'order_items' => 'required|array',
//            'order_items.*.product_id' => 'required|exists:products,id',
//            'order_items.*.category_id' => 'required|exists:categories,id',
//            'order_items.*.price' => 'required|numeric|min:1',
//            'order_items.*.quantity' => 'required|integer|min:1',
//            'order_items.*.rate_in' => 'nullable|numeric|min:0',
//            'cashAmount' => 'nullable|numeric|min:0',
        ];
    }

}
