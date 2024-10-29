<?php

namespace App\Http\Resources;

use App\Manager\ImageManager;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $slug
 * @property mixed $serial
 * @property mixed $status
 * @property mixed $photo
 * @property mixed $user
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $description
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    final public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'serial' => $this->serial,
            'status' => $this->status == 1 ? 'Active' : 'Inactive',
            'photo' => ImageManager::prepareImageUrl(Category::CATEGORY_THUMB_IMAGE_PATH, $this->photo),
            'photo_full' => ImageManager::prepareImageUrl(Category::CATEGORY_IMAGE_PATH, $this->photo),
            'created_by' => $this->user?->name,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDateTimeString() : 'Not updated'
        ];
    }
}
