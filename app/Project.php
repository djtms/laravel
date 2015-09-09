<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table  ='project';
    protected $fillable  = ['id','name','description','start_date','end_date','complete_percent','project_status_id','created_at','creator_id','modified'];
    public $timestamps = false;
}
