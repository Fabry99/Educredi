<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSessions extends Model
{
    protected $table = 'user_sessions'; 
    protected $fillable = ['id','user_id','started_at','ended_at','ip_address','user_agent','created_at','updated_at'];
}
