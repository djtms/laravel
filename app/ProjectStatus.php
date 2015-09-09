<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $table = 'project_status';
    protected $fillable = ['id','name','description'];
    public $timestamps = false;
}
