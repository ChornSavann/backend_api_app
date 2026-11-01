<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'promotion_code' => 'required|string|unique:product_promotions,promotion_code|max:50',
            'quantity' => 'nullable|integer|min:1',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ];
    }
}