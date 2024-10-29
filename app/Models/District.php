<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * @return Builder[]|Collection
     */
    final public function getDistrictByDivisionId(int $id): Collection|array
    {
        return self::query()->select('id', 'name')->where('division_id',$id)->get();
    }
}
