<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubDomain extends Model
{
    protected $table = 'subdomain';
    protected $fillable  = array('id','title','custom_domain','status','created_at','created_by');
    public $timestamps = false;
}
