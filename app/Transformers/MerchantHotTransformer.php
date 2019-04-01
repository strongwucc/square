<?php

namespace App\Transformers;

use App\Models\O2oMerchantHot;
use League\Fractal\TransformerAbstract;

class MerchantHotTransformer extends TransformerAbstract
{
    public function transform(O2oMerchantHot $merchantHot)
    {
        return [
            'mer_id' => $merchantHot->mer_id,
            'name' => $merchantHot->mer_name,
            'pic' => $merchantHot->merr_pic,
            'per_cost' => $merchantHot->per_cost
        ];
    }
}
