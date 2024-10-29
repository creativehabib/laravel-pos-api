<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $prepareAttributeData)
 */
class ProductAttribute extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','attribute_id','attribute_value_id'];

    /**
     * @param array $input
     * @param Product $product
     * @return void
     */
    final public function storeAttributeData(array $input, Product $product): void
    {
        $attribute_data = $this->prepareAttributeData($input, $product);
        foreach ($attribute_data as $attribute){
            self::create($attribute);
        }

    }

    /**
     * @param array $input
     * @return array
     */
    private function prepareAttributeData(array $input, Product $product): array
    {
        $attribute_data = [];
        foreach ($input as $value){
            $data['product_id'] = $product->id;
            $data['attribute_id'] = $value['attribute_id'];
            $data['attribute_value_id'] = $value['value_id'];
            $attribute_data[] = $data;
        }
        return $attribute_data;
    }

    /**
     * @return BelongsTo
     */
    final public function attributes(): BelongsTo
    {
        return $this->belongsTo(Attribute::class,'attribute_id');
    }

    /**
     * @return BelongsTo
     */
    final public function attribute_value(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class,'attribute_value_id');
    }
}
