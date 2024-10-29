<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $created_at
 * @property mixed $customer
 * @property mixed $updated_at
 * @property mixed $order_number
 * @property mixed $order_status
 * @property mixed $payment_method
 * @property mixed $sales_manager
 * @property mixed $shop
 * @property mixed $discount
 * @property mixed $due_amount
 * @property mixed $total
 * @property mixed $sub_total
 * @property mixed $paid_amount
 * @property mixed $quantity
 * @property mixed $payment_status
 */
class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payment_status = "Unpaid";
        if ($this->payment_status == Order::PAYMENT_STATUS_PAID){
            $payment_status = 'Paid';
        }elseif ($this->payment_status == Order::PAYMENT_STATUS_PARTIALLY_PAID){
            $payment_status = "Partially Paid";
        }
        return [
            'id'                    => $this->id,
            'created_at'            => $this->created_at->toDayDateTimeString(),
            'updated_at'            => $this->created_at != $this->updated_at ? $this->updated_at->toDayTimeString(): 'Not Updated',
            'customer_name'         => $this->customer?->name,
            'customer_phone'        => $this->customer?->phone,
            'order_number'          => $this->order_number,
            'order_status'          => $this->order_status,
            'order_status_string'   => $this->order_status == Order::STATUS_COMPLETED ? 'Completed' : 'Pending',
            'payment_method'        => $this->payment_method?->name,
            'payment_status'        => $payment_status,
            'sales_manager'         => $this->sales_manager?->name,
            'shop'                  => $this->shop?->name,
            'discount'              => $this->discount,
            'due_amount'            => $this->due_amount,
            'paid_amount'           => $this->paid_amount,
            'quantity'              => $this->quantity,
            'sub_total'             => $this->sub_total,
            'total'                 => $this->total,
        ];
    }
}
