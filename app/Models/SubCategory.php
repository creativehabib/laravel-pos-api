<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $photo
 */
class SubCategory extends Model
{
    use HasFactory;
    public const CATEGORY_IMAGE_PATH = 'images/uploads/sub_category/';
    public const CATEGORY_THUMB_IMAGE_PATH = 'images/uploads/sub_category_thumb/';

    protected $fillable = [
        'name', 'category_id', 'slug', 'serial', 'status', 'description', 'photo','user_id'
    ];

    /**
     * @param array $input
     * @return Builder|Model
     */
    final public function storeSubCategory(array $input):Builder|Model
    {
        return self::query()->create($input);
    }

    final public function getAllSubCategories(array $input): LengthAwarePaginator
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
     * @return BelongsTo
     */
    final public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param $category_id
     * @return array|Collection
     */

    public function getCategoryNameAndId(int $category_id): array|Collection
    {
        return self::query()->select('id', 'name')->where('category_id', $category_id)->get();
    }

    /**
     * @return array|Collection
     */

    public function getSubCategoryIdAndName(): array|Collection
    {
        return self::query()->select('id', 'name','category_id')->get();
    }

}
