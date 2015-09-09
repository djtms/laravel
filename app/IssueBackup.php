<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueBackup extends Model
{
    protected $table = 'issue_backup';
    protected $fillable = ['id','issue_id','code','name','description','project_id','type_id','status_id',
    					   'assignee_id','creator_id','priority','parent_issue_id','start_date','end_start',
    					   'complete_percent','attachment','created_at','modified','comment','modifier',
    					   'watchers','hours'];
    public $timestamps = false;
}
