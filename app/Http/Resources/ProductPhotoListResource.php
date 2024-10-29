<?php

namespace App\Http\Resources;

use App\Manager\ImageManager;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPhotoListResource extends JsonResource
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
            'photo' => ImageManager::prepareImageUrl(ProductPhoto::THUMB_PHOTO_UPLOAD_PATH, $this->photo),
            'photo_orginal' => ImageManager::prepareImageUrl(ProductPhoto::PHOTO_UPLOAD_PATH, $this->photo)
        ];
    }
}
