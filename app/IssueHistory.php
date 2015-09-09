<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueHistory extends Model
{
    protected $table = 'issue_history';
    protected $fillable = ['id','issue_id','assigner_from_id','assigner_to_id','status_from_id','created_at'];
    public $timestamps = false;
}
