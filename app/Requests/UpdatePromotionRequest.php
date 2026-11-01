<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'product_id' => 'sometimes|exists:products,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'promotion_code' => 'sometimes|string|unique:product_promotions,promotion_code,' . $id . '|max:50',
            'quantity' => 'nullable|integer|min:0',
            'discount_type' => 'sometimes|in:percentage,fixed',
            'discount_value' => 'sometimes|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ];
    }
}