<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadpostAuth extends Model
{
    protected $table ='radpostauth';
    protected $fillable  = array('id','user','pass','reply','AcctStartTime','AcctSessionId','Ip','Nas_Id','Client_Mac','lastudate');
    public $timestamps = false;
}
