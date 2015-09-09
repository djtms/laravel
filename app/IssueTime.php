<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueTime extends Model
{
    protected $table = 'issue_time';
    protected $fillable = ['id','issue_id','date','hours','description'];
    public $timestamps = false;
}
