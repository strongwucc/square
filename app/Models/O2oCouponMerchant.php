<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oCouponMerchant extends Model
{
    protected $table = 'o2o_cps_mrt_rel';
    public $timestamps = false;

    public function coupon()
    {
        return $this->belongsTo(O2oCoupon::class, 'pcid', 'pcid');
    }

    public function merchant()
    {
        return $this->belongsTo(O2oMerchant::class, 'mer_id', 'mer_id');
    }
}
