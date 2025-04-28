<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisores extends Model
{
    protected $table = 'supervisores';
    protected $fillable = ['id','nombre'];
}
