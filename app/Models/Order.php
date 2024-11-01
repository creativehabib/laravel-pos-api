<?php

namespace App\Models;

use App\Manager\OrderManager;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSED = 2;
    public const STATUS_COMPLETED = 3;
    public const SHIPMENT_STATUS_COMPLETED = 1;

    public const PAYMENT_STATUS_PAID = 1;
    public const PAYMENT_STATUS_PARTIALLY_PAID = 2;
    public const PAYMENT_STATUS_UNPAID = 3;


    public function getAllOrders(array $input, $auth)
    {
        $is_admin  = $auth->guard('admin')->check();
        $query = self::query();
        $query->with([
            'customer:id,name,phone',
            'payment_method:id,name',
            'sales_manager:id,name',
            'shop:id,name',
        ]);
        if (!$is_admin){
            $query->where('shop_id', $auth->user()->shop_id);
        }
        return $query->paginate(4);
    }

    /**
     * @param array $input
     * @param $auth
     * @return array|Builder|Model
     * @throws Exception
     */
    public function placeOrder(array $input, $auth)
    {
        $order_data = $this->prepareData($input, $auth);
        if (isset($order_data['error_description'])){
            return $order_data;
        }
        $order = self::query()->create($order_data['order_data']);
        (new OrderDetails())->storeOrderDetails($order_data['order_details'], $order);
        (new Transaction())->storeTransaction($input, $order, $auth);
        return $order;

    }

    /**
     * @param array $input
     * @param $auth
     * @return array
     * @throws Exception
     */

    public function prepareData(array $input, $auth)
    {
        $price = OrderManager::handle_order_data($input);
        if (isset($price['error_description'])){
            return $price;
        }else{
            $order_data = [
                'customer_id'       => $input['order_summary']['customer_id'] ?? 0,
                'sales_manager_id'  => $auth->id,
                'shop_id'           => $auth->shop_id ?? 1,
                'sub_total'         => $price['sub_total'],
                'discount'          => $price['discount'],
                'total'             => $price['total'],
                'quantity'          => $price['quantity'],
                'paid_amount'       => $input['order_summary']['paid_amount'],
                'due_amount'        => $input['order_summary']['due_amount'],
                'order_status'      => self::STATUS_COMPLETED,
                'order_number'      => isset($auth->shop_id) ? OrderManager::generateOrderNumber($auth->shop_id) : 'FBD'.'1'.Carbon::now()->format('dmy').random_int(100,999),
                'payment_method_id' =>$input['order_summary']['payment_method_id'],
                'payment_status'    => OrderManager::decidePaymentStatus($price['total'], $input['order_summary']['paid_amount']),
                'shipment_status'   => self::SHIPMENT_STATUS_COMPLETED,
            ];
            return ['order_data'    => $order_data, 'order_details'=>$price['order_details']];
        }

    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo
     */
    public function payment_method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    /**
     * @return BelongsTo
     */
    public function sales_manager(): BelongsTo
    {
        return $this->belongsTo(SalesManager::class);
    }
    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @return HasMany
     */
    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetails::class);
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getAllOrdersForReport(bool $is_admin, int $sales_admin_id, array $columns = ['*'])
    {
        $query = DB::table('orders')->select($columns);
        if(!$is_admin){
            $query->where('sales_manager_id', $sales_admin_id);
        }
        $orders = $query->get();
        return collect($orders);
    }

}
