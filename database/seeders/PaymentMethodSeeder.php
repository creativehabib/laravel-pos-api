<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payment_method =  [
            [
                'name' => 'Cash',
                'status' => 1,
                'account_number' => '12432343'
            ],
            [
                'name' => 'bKash',
                'status' => 1,
                'account_number' => '12432343'
            ],
            [
                'name' => 'Nagad',
                'status' => 1,
                'account_number' => '12432343'
            ],
            [
                'name' => 'Rocket',
                'status' => 1,
                'account_number' => '12432343'
            ],
        ];
        PaymentMethod::insert($payment_method);
    }
}
