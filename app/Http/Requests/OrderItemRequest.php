<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
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
            'order_id' => 'required|exists:orders,id', // Ensure the order exists
            'product_id' => 'required|exists:products,id', // Ensure the product exists
            'quantity' => 'required|integer|min:1', // Quantity must be an integer and at least 1
            'price' => 'required|numeric|min:0.01', // Price must be numeric and greater than 0
            'total' => 'required|numeric|min:0.01', // Total must be numeric and greater than 0
        ];
    }
}
