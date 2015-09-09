<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = 'user_device';
    protected $fillable = ['id','name','location_id','email','gender','birthday','social_network','day_added'];
}
