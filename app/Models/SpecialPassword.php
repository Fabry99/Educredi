<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialPassword extends Model
{
    protected $table = 'password_special';
    protected $fillable =['id','password','created_at','update_at'];
}
