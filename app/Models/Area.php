<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * @param int $id
     * @return Builder[]|Collection
     */
    public function getAreaByDistrictId(int $id): Collection|array
    {
        return self::query()->select('id', 'name')->where('district_id',$id)->get();
    }
}
