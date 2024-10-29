<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    /**
     * @return Builder[]|Collection
     */
    final public function getPaymentMethodList(): Collection|array
    {
        return self::query()->select(['id','name','account_number'])->get();
    }
}
