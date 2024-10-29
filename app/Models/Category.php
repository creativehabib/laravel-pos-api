<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property mixed $photo
 */
class Category extends Model
{
    use HasFactory;
    public const CATEGORY_IMAGE_PATH = 'images/uploads/category/';
    public const CATEGORY_THUMB_IMAGE_PATH = 'images/uploads/category_thumb/';

    protected $fillable = [
        'name', 'slug', 'serial', 'status', 'description', 'photo','user_id'
    ];

    /**
     * @param array $input
     * @return Builder|Model
     */
    final public function storeCategory(array $input):Builder|Model
    {
        return self::query()->create($input);
    }

    /**
     * @return LengthAwarePaginator
     */
    final public function getAllCategories(array $input): LengthAwarePaginator
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
     * @return Collection
     */
    public function getCategoryNameAndId(): Collection
    {
        return self::query()->select('id', 'name')->get();
    }

    /**
     * @return BelongsTo
     */
    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
