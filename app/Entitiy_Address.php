<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Entitiy_Address extends Model
{
    protected $table="entitiy_address";
    protected $fillable  = ['street', 'city', 'state', 'zip', 'country_id', 'tmo','default','entity_id'];

    public static function hasTmo($tmo)
    {
        if($tmo == ''){
            return [];
        }
       $res = DB::select('select tmo from entitiy_address where tmo = '.$tmo);
        return $res;
    }
}
