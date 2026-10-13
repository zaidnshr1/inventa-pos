<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'in:CASH,QRIS,DEBIT,TRANSFER'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'cart_items' => ['required', 'array', 'min:1'],
            'cart_items.*.product_id' => ['required', 'exists:products,id'],
            'cart_items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
