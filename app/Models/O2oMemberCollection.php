<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oMemberCollection extends Model
{
    protected $table = 'o2o_member_collection';
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
        return $query->orderBy('id', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function merchant()
    {
        return $this->hasOne('App\Models\O2oMerchant', 'mer_id', 'mer_id');
    }
}
