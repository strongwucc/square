<?php

namespace App\Transformers;

use App\Models\O2oMerchantType;
use League\Fractal\TransformerAbstract;

class MerchantTypeTransformer extends TransformerAbstract
{
    public function transform(O2oMerchantType $merchantType)
    {
        return [
            'code' => $merchantType->type_code,
            'pcode' => $merchantType->pcode,
            'name' => $merchantType->type_name,
            'pic' => $merchantType->tag_pic
        ];
    }
}
