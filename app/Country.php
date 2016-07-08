<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table="country";

    /**
     * @return array
     */
    public function User()
    {
        return $this->hasMany('User');
        
    }
}
