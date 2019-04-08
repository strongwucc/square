<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\O2oMemberPoint;

class PointTransformer extends TransformerAbstract
{

    public function transform(O2oMemberPoint $point)
    {

        return [
            'related_id' => $point->related_id,
            'change_point' => $point->change_point,
            'consume_point' => $point->consume_point,
            'addtime' => $point->addtime,
            'reason' => $point->reason,
            'pay_reason' => $point->pay_reason
        ];
    }
}
