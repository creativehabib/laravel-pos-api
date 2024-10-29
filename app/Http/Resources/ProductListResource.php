<?php

namespace App\Http\Resources;

use App\Manager\ImageManager;
use App\Manager\PriceManager;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property mixed $user
 */
class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'description'           => $this->description,
            'cost'                  => $this->cost . PriceManager::CURRENCY_SYMBOL,
            'price'                 => number_format($this->price) . PriceManager::CURRENCY_SYMBOL,
            'original_price'        => $this->price,
            'selling_price'         => PriceManager::calculate_selling_price($this->price, $this->discount_percent, $this->discount_fixed, $this->discount_start, $this->discount_end),
            'sku'                   => $this->sku,
            'stock'                 => $this->stock,
            'discount_fixed'        => $this->discount_fixed . PriceManager::CURRENCY_SYMBOL,
            'discount_percent'      => $this->discount_percent . '%',
            'discount_start'        => $this->discount_start != null ? Carbon::create($this->discount_start)->toDayDateTimeString() : null,
            'discount_end'          => $this->discount_end != null ? Carbon::create($this->discount_end)->toDayDateTimeString() : null,
            'slug'                  => $this->slug,
            'status'                => $this->status == Product::STATUS_ACTIVE ? 'Active' : 'Inactive',
            'created_at'            => $this->created_at->toDayDateTimeString(),
            'updated_at'            => $this->created_at != $this->updated_at ? $this->updated_at->toDateTimeString() : 'Not updated',

            'brand'         => $this->brand?->name,
            'category'      => $this->category?->name,
            'sub_category'  => $this->sub_category?->name,
            'supplier'      => $this->supplier ? $this->supplier?->name . ', ' . $this->supplier?->phone : null,
            'country'       => $this->country?->name,
            'created_by'    => $this->created_by?->name,
            'updated_by'    => $this->updated_by?->name,
            'photo'         => ImageManager::prepareImageUrl(ProductPhoto::THUMB_PHOTO_UPLOAD_PATH, $this->primary_photo?->photo),
            'attributes'    => ProductAttributeListResource::collection($this->product_attributes),
        ];
    }
}
