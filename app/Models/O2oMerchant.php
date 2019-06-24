<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oMerchant extends Model
{
    protected $table = 'o2o_merchant';
    protected $primaryKey = 'id';
    public $timestamps = false;

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
        return $query->orderBy('orders_total', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('last_time', 'desc');
    }

    public function coupons()
    {
        return $this->hasMany('App\Models\O2oCoupon', 'mer_id', 'mer_id');
    }

    public function b2cOrders()
    {
        return $this->hasMany('App\Models\B2cOrder', 'merchant_bn', 'mer_id');
    }

    public function selectHotOptions()
    {
        $hot_model = new O2oMerchantHot();

        $hots = $hot_model->select('mer_id')->get();

        $hot_ids = [];

        foreach ($hots as $hot) {
            $hot_ids[] = $hot->mer_id;
        }

        $merchants = $this->whereNotIn('mer_id', $hot_ids)->get();

        $options = [];

        foreach ($merchants as $merchant) {
            $key = $merchant->mer_id;
            $options[$key] = $merchant->mer_name;
        }
        return $options;

    }

    public function getTitleAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = implode(',', $value);
    }
}
