<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable  = ['id','is_read','subject','content','project_id','issue_id','is_visible'];
    public $timestamps = false;
}
