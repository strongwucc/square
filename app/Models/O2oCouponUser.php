<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oCouponUser extends Model
{
    protected $table = 'o2o_promotion_coupon_user';
    public $timestamps = false;

    public function merchant()
    {
        return $this->hasOne('App\Models\O2oMerchant', 'mer_id', 'mer_id');
    }
}
