<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table="logs";
    protected $fillable = ['table', 'user_id', 'old_value', 'new_value', 'when', 'action'];

}
