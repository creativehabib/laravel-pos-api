<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static create($supplier)
 */
class SalesManager extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $hidden = ['password'];

    protected $fillable = [
        'bio',
        'email',
        'photo',
        'nid_photo',
        'name',
        'phone',
        'status',
        'user_id',
        'shop_id',
        'nid',
        'password'
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_ACTIVE_TEXT = "Active";
    public const STATUS_INACTIVE = 0;
    public const STATUS_INACTIVE_TEXT = "Inactive";
    public const PHOTO_WIDTH = 800;
    public const PHOTO_HEIGHT = 800;
    public const PHOTO_THUMB_WIDTH = 200;
    public const PHOTO_THUMB_HEIGHT = 200;
    public const PHOTO_UPLOAD_PATH = 'images/uploads/sales_manager/';
    public const THUMB_PHOTO_UPLOAD_PATH = 'images/uploads/sales_manager_thumb/';


    /**
     * @param array $input
     * @param $auth
     * @return array
     */
    final public function prepareData(array $input, $auth): array
    {
        $sales_manager['bio']       = $input['bio'] ?? null;
        $sales_manager['email']     = $input['email'] ?? null;
        $sales_manager['name']      = $input['name'] ?? null;
        $sales_manager['phone']     = $input['phone'] ?? null;
        $sales_manager['status']    = $input['status'] ?? 0;
        $sales_manager['user_id']   = $auth->id();
        $sales_manager['shop_id']   = $input['shop_id'] ?? null;
        $sales_manager['nid']       = $input['nid'] ?? null;
        $sales_manager['password']  = Hash::make($input['password']);
        return $sales_manager;
    }

    /**
     * @return MorphOne
     */
    final public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @param $input
     * @return LengthAwarePaginator
     */
    final public function getSalesManagerList($input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with('address',
            'address.division:id,name',
            'address.district:id,name',
            'address.area:id,name',
            'user:id,name',
            'shop:id,name'
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
     * @param array $input
     * @return Builder|Model|null
     */
    final public function getUserByEmailOrPhone(array $input): Builder|Model|null
    {
        return self::query()->where('email', $input['email'])
            ->orWhere('phone', $input['email'])->first();
    }


    /**
     * @return MorphOne
     */
    final public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class,'transactionable');
    }
}
