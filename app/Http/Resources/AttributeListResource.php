<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $status
 * @property mixed $user
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $value
 */
class AttributeListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status == 1 ? 'Active' : 'Inactive',
            'original_status' => $this->status,
            'created_by' => $this->user?->name,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDateTimeString() : 'Not updated',
            'value' => AttributeValueResource::collection( $this->value)
        ];
    }
}
