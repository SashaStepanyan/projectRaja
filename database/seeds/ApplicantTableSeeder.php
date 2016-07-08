<?php

use Illuminate\Database\Seeder;

class ApplicantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('applicant')->insert(
            [
                [
                    'name' => 'Applicant',
                    'status'=>1
                ],
                [
                    'name' => 'Client',
                    'status'=>1
                ],
                [
                    'name' => 'Foreign Counsel',
                    'status'=>1
                ]


            ]
        );
    }
}
