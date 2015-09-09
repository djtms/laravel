<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class DeviceOnlineOfflineStatus extends Model
{
    protected $table = 'device_online_offline_status';
    protected $fillable = ['id','nasid','device_id','status','last_heartbeat'];
    public $timestamps = false;
}
