<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functions extends Model
{
    protected $table = 'functions';
    protected $fillable = ['id','name','description','category'];
    public $timestamps = false;
}
