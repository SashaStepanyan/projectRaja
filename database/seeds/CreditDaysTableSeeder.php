<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CreditDaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('credit_days')->insert(
            [
                [
                    'day' => 0,
                    'status'=>1

                ],
                [
                    'day' => 15,
                    'status'=>1
                ],
                [
                    'day' => 30,
                    'status'=>1
                ],
                [
                    'day' => 45,
                    'status'=>1
                ],
                [
                    'day' => 60,
                    'status'=>1
                ],
                [
                    'day' => 75,
                    'status'=>1
                ],
                [
                    'day' => 90,
                    'status'=>1
                ],
                [
                    'day' => 120,
                    'status'=>1
                ]
            ]
        );
    }
}
