<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules():array
    {
        return [
            'name' => 'string|required|min:3|max:255',
            'slug' => 'string|required|min:3|max:255|unique:products',
            'sku' => 'string|required|min:3|max:255|unique:products',
            'brand_id' => 'numeric',
            'country_id' => 'numeric',
            'sub_category_id' => 'numeric',
            'supplier_id' => 'numeric',
            'discount_fixed' => 'numeric',
            'discount_percent' => 'numeric',
            'category_id' => 'required|numeric',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'status' => 'required|numeric',
            'stock' => 'required|numeric',
            'description' => 'required|max:1000|min:10',
            'attributes' => 'array',
            'specification' => 'array',
        ];
    }
}
