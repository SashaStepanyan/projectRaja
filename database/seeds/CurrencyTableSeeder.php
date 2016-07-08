<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currency')->insert(
            [
            [
                'name' => 'USD',
                'status'=>1
            ],
            [
                'name' => 'EUR',
                'status'=>1
            ],
            [
                'name' => 'INR',
                'status'=>1
        ]
                ]
        );
    }
}
