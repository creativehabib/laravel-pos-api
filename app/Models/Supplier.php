<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @method static create(array $supplier)
 * @property mixed $logo
 */
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['details', 'email', 'logo', 'name', 'phone', 'status', 'user_id'];

    public const STATUS_ACTIVE = 1;
    public const STATUS_ACTIVE_TEXT = "Active";
    public const STATUS_INACTIVE = 0;
    public const STATUS_INACTIVE_TEXT = "Inactive";
    public const LOGO_WIDTH = 800;
    public const LOGO_HEIGHT = 800;
    public const LOGO_THUMB_WIDTH = 200;
    public const LOGO_THUMB_HEIGHT = 200;

    public const IMAGE_UPLOAD_PATH = 'images/uploads/supplier/';
    public const THUMB_IMAGE_UPLOAD_PATH = 'images/uploads/supplier_thumb/';

    /**
     * @param array $input
     * @param $auth
     * @return array
     */
    final public function prepareData(array $input, $auth): array
    {
        $supplier['details'] = $input['details'] ?? '';
        $supplier['email'] = $input['email'] ?? '';
        $supplier['name'] = $input['name'] ?? '';
        $supplier['phone'] = $input['phone'] ?? '';
        $supplier['status'] = $input['status'] ?? '';
        $supplier['user_id'] = $auth->id();
        return $supplier;
    }

    /**
     * @return MorphOne
     */
    final public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * @param $input
     * @return LengthAwarePaginator
     */
    final public function getSupplierList($input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with('address',
            'address.division:id,name',
            'address.district:id,name',
            'address.area:id,name',
            'user:id,name'
        );
        if (!empty($input['search'])){
            $query->where('name', 'like', '%' .$input['search']. '%')
            ->orWhere('phone', 'like', '%' .$input['search']. '%')
            ->orWhere('email', 'like', '%' .$input['search']. '%');
        }
        if (!empty($input['order_by'])){
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }
       return $query->paginate($per_page);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Builder[]|Collection
     */
    final public function getSupplierSelectList(): Collection|array
    {
        return self::query()
            ->select('id', 'name', 'phone')
            ->where('status', self::STATUS_ACTIVE)
            ->get();
    }
}
