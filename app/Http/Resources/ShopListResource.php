<?php

namespace App\Http\Resources;

use App\Manager\ImageManager;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 * @property mixed $phone
 * @property mixed $details
 * @property mixed $user
 * @property mixed $status
 * @property mixed $logo
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $address
 */
class ShopListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'details' => $this->details,
            'created_by' => $this->user?->name,
            'status' => $this->status == Shop::STATUS_ACTIVE ? Shop::STATUS_ACTIVE_TEXT : Shop::STATUS_INACTIVE_TEXT,
            'logo' => ImageManager::prepareImageUrl(Shop::THUMB_IMAGE_UPLOAD_PATH,$this->logo),
            'logo_full' => ImageManager::prepareImageUrl(Shop::IMAGE_UPLOAD_PATH,$this->logo),
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDateTimeString() : 'Not updated',
            'address' => new AddressListResource($this->address),
        ];
    }
}
