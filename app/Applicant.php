<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Applicant extends Model
{
    protected $table='applicant';

    public static function getAplicantsLike($data)
    {
        $aplicants =Applicant::where('name', 'like', '%' . $data . '%')->get();

        return $aplicants;
    }
}
