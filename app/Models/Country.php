<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $guarded = [];

    public const STATUS_ACTIVE = 1;

    /**
     * @return Builder[]|Collection
     */
    final public function getCountryNameAndId(): Collection|array
    {
        return self::query()->select('id','name')
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('name','asc')->get();
    }
}
