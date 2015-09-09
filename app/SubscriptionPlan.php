<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $table ='subscription_plan';
    protected $fillable  = array('subscription_id','user_id','plan_id','customer_id','number_of_allowed_device','interval','plan_name','amount','currency','trial_period_days','is_card','current_period_start','current_period_end','created_at','modified_at');
    public $timestamps = false;
}
