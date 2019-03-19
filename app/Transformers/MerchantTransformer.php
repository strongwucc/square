<?php

namespace App\Transformers;

use App\Models\O2oMerchant;
use League\Fractal\TransformerAbstract;

class MerchantTransformer extends TransformerAbstract
{
    public function transform(O2oMerchant $merchant)
    {
        return [
            'id' => $merchant->id,
            'mer_id' => $merchant->mer_id,
            'name' => $merchant->mer_name,
            'addr' => $merchant->mer_addr,
            'pic' => $merchant->mer_pic,
            'mobile' => $merchant->contact_mobile,
            'cost' => $merchant->per_cost,
            'title' => $merchant->title,
            'detail' => $merchant->details,
            'open_time' => $merchant->open_time
        ];
    }
}
