<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(Object $order)
    {
        return [
            'order_no' => $order->order_no,
            'source' => $order->source,
            'pay_amount' => $order->pay_amount,
            'pay_result' => $order->pay_result,
            'tran_time' => date('Y-m-d H:i:s', $order->tran_time)
            // 'tran_time' => $order->tran_time
        ];
    }
}
