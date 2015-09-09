<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionLog extends Model
{
    protected $table = 'subscription_log';
    protected $fillable = array('id','account_code','event','data','created_at');
    public $timestamps = false;
}
