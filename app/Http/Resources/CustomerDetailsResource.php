<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'created_at'    => $this->created_at->toDayDateTimeString(),
            'updated_at'    => $this->created_at != $this->updated_at ? $this->updated_at->toDateTimeString() : 'Not updated',
        ];
    }
}