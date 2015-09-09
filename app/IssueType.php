<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    protected $table = 'issue_type';
    protected $fillable = ['id','name','description','color'];
    public $timestamps = false;
}
