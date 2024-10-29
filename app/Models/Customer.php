<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Request;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'email', 'phone'
    ];

    final public function store($input)
    {
        $customer = $this->prepareData($input);
        return self::query()->create($customer);
    }

    private function prepareData($input): array
    {
        return $customer_data = [
            'name' => $input['name'] ?? '',
            'email' => $input['email'] ?? '',
            'phone' => $input['phone'] ?? '',
        ];
    }

    public function getCustomerBySearch($search)
    {
        return self::query()
            ->select('id', 'name','phone')
            ->where('name','like','%'.$search['search'].'%')
            ->orWhere('phone','like','%'.$search['search'].'%')
            ->take(15)
            ->get();
    }

}
