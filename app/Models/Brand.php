<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    use HasFactory;
    public const BRAND_IMAGE_PATH = 'images/uploads/brand/';
    public const BRAND_THUMB_IMAGE_PATH = 'images/uploads/brand_thumb/';

    public const STATUS_ACTIVE = 1;

    protected $fillable = [
        'name', 'slug', 'serial', 'status', 'description', 'logo','user_id'
    ];

    /**
     * @param array $input
     * @return Builder|Model
     */
    final public function storeBrand(array $input):Builder|Model
    {
        return self::query()->create($input);
    }

    /**
     * @return LengthAwarePaginator
     */
    final public function getAllBrands(array $input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query();
        if(!empty($input['search'])){
            $query->where('name','like', '%'.$input['search'].'%');
        }
        if(!empty($input['order_by'])){
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }
        return $query->with('user:id,name')->paginate($per_page);
    }

    /**
     * @return BelongsTo
     */
    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Builder[]|Collection
     */
    final public function getBrandNameAndId(): Collection|array
    {
        return self::query()->select('id','name')->where('status', self::STATUS_ACTIVE)->get();
    }
}
