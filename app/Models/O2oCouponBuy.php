<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oCouponBuy extends Model
{
    protected $table = 'o2o_promotion_coupon_buy';

    const CREATED_AT = 'createtime';
    const UPDATED_AT = 'last_modified';

    public function scopeWithOrder($query, $order)
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }
    }

    public function scopeRecentReplied($query)
    {
        return $query->orderBy('createtime', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('last_modified', 'desc');
    }

    public function coupon()
    {
        return $this->hasOne('App\Models\O2oCoupon', 'pcid', 'pcid');
    }
}
