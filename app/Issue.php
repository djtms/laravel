<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'issue';
    protected $fillable = ['id','code','name','description','project_id','type_id','status_id','assignee_id',
    					   'creator_id','priority','parent_issue_id','child_ids','order_id','level',
    					   'start_date','end_date','complete_percent','attachment','created_at','modified',
    					   'watchers','hours'];
    public $timestamps = false;
}
