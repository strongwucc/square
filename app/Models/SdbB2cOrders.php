<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SdbB2cOrders extends Model
{
    protected $table = 'sdb_b2c_orders';
//    protected $primaryKey = 'order_id';
    public $timestamps = false;

    public function merchant()
    {
        return $this->belongsTo('App\Models\O2oMerchant', 'merchant_bn', 'mer_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\SdbB2cOrderItems', 'order_id', 'order_id');
    }
}
