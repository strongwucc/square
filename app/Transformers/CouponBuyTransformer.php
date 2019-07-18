<?php

namespace App\Transformers;

use App\Models\O2oCouponBuy;
use App\Models\O2oMerchant;
use League\Fractal\TransformerAbstract;
use PhpParser\Node\Expr\Cast\Object_;

class CouponBuyTransformer extends TransformerAbstract
{

    public function transform(O2oCouponBuy $coupon)
    {
        $dated = 0;
        $begin_date_time = '';
        $end_date_time = '';
        $left_days = '';

        if ($coupon->coupon->date_type == 'DATE_TYPE_FIX_TIME_RANGE') {
            $now = date('Y-m-d H:i:s', time());
            $dated = $coupon->coupon->end_timestamp < $now ? 1 : 0;
            $begin_date_time = date('Y.m.d', strtotime($coupon->coupon->begin_timestamp));
            $end_date_time = date('Y.m.d', strtotime($coupon->coupon->end_timestamp));
            $left_days = floor((strtotime($coupon->coupon->end_timestamp) - time()) / (3600 * 24));
        } else {
            $dated = strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600 < time() ? 1 : 0;
            $begin_date_time = date('Y.m.d', strtotime($coupon->createtime) + $coupon->coupon->fixed_begin_term * 24 * 3600);
            $end_date_time = date('Y.m.d', strtotime(strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600));
            $left_days = floor((strtotime(strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600) - time()) / (3600 * 24));
        }

        $order = [];

        if ($coupon->order) {
            $order['orderNo'] = $coupon->order->order_no;
            $order['payAmount'] = $coupon->order->pay_amount / 100;
            $order['tranRime'] = $coupon->order->tran_time;
        }

        $merchants = [];

        if ($coupon->coupon->mer_id) {
            $merchants = O2oMerchant::whereIn('mer_id', $coupon->coupon->mer_id)->get()->toArray();
        }

        return [
            'id' => $coupon->pcid,
            'cid' => $coupon->cid,
            'qrcode' => $coupon->qrcode,
            'pay_status' => $coupon->pay_status,
            'buy_status' => $coupon->buy_status,
            'use_status' => $coupon->use_status,
            'mer_id' => $coupon->coupon->mer_id,
            'merchants' => $merchants,
            'brand_name' => $coupon->coupon->brand_name,
            'card_type' => $coupon->coupon->card_type,
            'logo' => $coupon->coupon->logo_url,
            'title' => $coupon->coupon->title,
            'sub_title' => $coupon->coupon->sub_title,
            'notice' => $coupon->coupon->notice,
            'description' => $coupon->coupon->description,
            'grant_quantity' => $coupon->coupon->grant_quantity,
            'quantity' => $coupon->coupon->quantity,
            'date_type' => $coupon->coupon->date_type,
            'begin_timestamp' => $coupon->coupon->begin_timestamp,
            'end_timestamp' => $coupon->coupon->end_timestamp,
            'fixed_term' => $coupon->coupon->fixed_term,
            'fixed_begin_term' => $coupon->coupon->fixed_begin_term,
            'service_phone' => $coupon->coupon->service_phone,
            'get_limit' => $coupon->coupon->get_limit,
            'deal_detail' => $coupon->coupon->deal_detail,
            'least_cost' => $coupon->coupon->least_cost,
            'reduce_cost' => $coupon->coupon->reduce_cost,
            'discount' => $coupon->coupon->discount,
            'gift' => $coupon->coupon->gift,
            'default_detail' => $coupon->coupon->default_detail,
            'market_price' => $coupon->coupon->market_price,
            'sale_price' => $coupon->coupon->sale_price,
            'dated' => $dated,
            'begin_date_time' => $begin_date_time,
            'end_date_time' => $end_date_time,
            'left_days' => $left_days,
            'order' => $order ? $order : (object)null
        ];
    }
}
