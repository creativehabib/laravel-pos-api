<?php

namespace App\Http\Resources;

use App\Manager\PriceManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListForBarCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'price'     => number_format($this->price) . PriceManager::CURRENCY_SYMBOL,
            'selling_price'=> PriceManager::calculate_selling_price($this->price,$this->discount_percent,$this->discount_fixed,$this->discount_start,$this->discount_end),
            'sku'       => $this->sku
        ];
    }
}
