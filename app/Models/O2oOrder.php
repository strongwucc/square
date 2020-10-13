<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oOrder extends Model
{
    protected $table = 'mch_etongpay_order';

    protected $fillable = [
        'order_no', 'mch_id', 'member_id', 'source', 'shopno', 'pay_amount', 'pay_type', 'scan_pay_type', 'pay_result', 'pay_info', 'cert_no', 'buy_mobile', 'tran_time', 'etone_order_id', 'remark'
    ];

    public $timestamps = false;

}
