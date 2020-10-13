<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2cOrder extends Model
{
    protected $table = 'sdb_b2c_orders';

    public function member()
    {
        return $this->belongsTo('App\Models\O2oMember', 'member_id', 'platform_member_id');
    }
}
