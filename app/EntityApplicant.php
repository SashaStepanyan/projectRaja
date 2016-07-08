<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityApplicant extends Model
{
    protected $table="entity_applicant";
    protected $fillable  = ['applicant_id','entities_id'];



    public function Applicant(){
        return $this->belongsTo('App\Applicant');
    }
}
