<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oMember extends Model
{
    protected $table = 'o2o_member';
    protected $primaryKey = 'member_id';
    public $timestamps = false;

    public function points()
    {
        return $this->hasMany('App\Models\O2oMemberPoint', 'platform_member_id', 'platform_member_id');
    }

    public function coupons()
    {
        return $this->hasMany('App\Models\O2oCouponBuy', 'member_id', 'platform_member_id');
    }
}
