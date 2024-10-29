<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @method static create(array $prepareData)
 */
class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'category_id',
        'country_id',
        'sub_category_id',
        'supplier_id',
        'created_by_id',
        'updated_by_id',
        'cost',
        'description',
        'discount_fixed',
        'discount_percent',
        'discount_start',
        'discount_end',
        'name',
        'price',
        'sku',
        'slug',
        'status',
        'stock',
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    /**
     * @param array $input
     * @param int $auth_id
     * @return mixed
     */
    final public function storeProduct(array $input, int $auth_id): mixed
    {
        return self::create($this->prepareData($input, $auth_id));
    }

    /**
     * @param array $input
     * @param int $auth_id
     * @return array
     */
    private function prepareData(array $input, int $auth_id): array
    {
        return [
            'brand_id'          => $input['brand_id'] ?? 0,
            'category_id'       => $input['category_id'] ?? 0,
            'cost'              => $input['cost'] ?? 0,
            'country_id'        => $input['country_id'] ?? 0,
            'description'       => $input['description'] ?? '',
            'discount_end'      => $input['discount_end'] ?? null,
            'discount_fixed'    => $input['discount_fixed'] ?? 0,
            'discount_percent'  => $input['discount_percent'] ?? 0,
            'discount_start'    => $input['discount_start'] ?? null,
            'name'              => $input['name'] ?? '',
            'price'             => $input['price'] ?? 0,
            'sku'               => $input['sku'] ?? '',
            'slug'              => $input['slug'] ? Str::slug($input['slug']) : '',
            'status'            => $input['status'] ?? 0,
            'stock'             => $input['stock'] ?? 0,
            'sub_category_id'   => $input['sub_category_id'] ?? 0,
            'supplier_id'       => $input['supplier_id'] ?? 0,
            'created_by_id'     => $auth_id,
            'updated_by_id'     => $auth_id,
        ];
    }

    /**
     * @param int $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    final public function getProductByID(int $id): Model|Collection|Builder|array|null
    {
        return self::query()->with('primary_photo')->findOrFail($id);
    }

    /**
     * @param array $input
     * @return LengthAwarePaginator
     */

    final public function getProductList(array $input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with([
            'category:id,name',
            'sub_category:id,name',
            'brand:id,name',
            'country:id,name',
            'supplier:id,name,phone',
            'created_by:id,name',
            'updated_by:id,name',
            'primary_photo',
            'product_attributes',
            'product_attributes.attributes',
            'product_attributes.attribute_value',
        ]);
        if (!empty($input['search'])){
            $query->where('name', 'like', '%' .$input['search']. '%')
                ->orWhere('sku', 'like', '%' .$input['search']. '%');
        }
        if (!empty($input['order_by'])){
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }
        return $query->paginate($per_page);
    }

    /**
     * @return BelongsTo
     */
    final public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    final public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * @return BelongsTo
     */
    final public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * @return BelongsTo
     */
    final public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * @return BelongsTo
     */
    final public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * @return BelongsTo
     */
    final public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * @return BelongsTo
     */
    final public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * @return HasOne
     */
    final public function primary_photo(): HasOne
    {
        return $this->hasOne(ProductPhoto::class)->where('is_primary',1);
    }

    /**
     * @return HasMany
     */
    final public function product_attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }


    public function getProductForBarCode($input)
    {
        $query = self::query()->select('id','name','sku','price','discount_end','discount_fixed','discount_percent','discount_start');
        if (isset($input['name'])){
            $query->where('name', 'like', '%'.$input['name'].'%');
        }
        if (isset($input['category_id'])){
            $query->where('category_id', $input['category_id']);
        }
        if (isset($input['sub_category_id'])){
            $query->where('sub_category_id', $input['sub_category_id']);
        }
        return $query->get();
    }

    public function getAllProduct($columns = ['*'])
    {
        $products = DB::table('products')->select($columns)->get();
        return collect($products);
    }

    /**
     * @return HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->where('is_primary',0);
    }


}
