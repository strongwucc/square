<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform($order)
    {
        $order_no = $order->order_no;
        $order_arr = explode('-',$order_no);
        $order_id = isset($order_arr[1]) ? $order_arr[1] : $order_arr[0];

        return [
            'order_no' => $order_id,
            'source' => $order->source,
            'pay_amount' => $order->pay_amount,
            'pay_result' => $order->pay_result,
            'tran_time' => date('Y-m-d H:i:s', $order->tran_time),
            'platform' => $order->platform,
            'status' => $order->status
            // 'tran_time' => $order->tran_time
        ];
    }
}
