<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\O2oMemberCollection;

class CollectionTransformer extends TransformerAbstract
{

    public function transform(O2oMemberCollection $collection)
    {

        return [
            'id' => $collection->merchant->id,
            'mer_id' => $collection->merchant->mer_id,
            'name' => $collection->merchant->mer_name,
            'addr' => $collection->merchant->mer_addr,
            'pic' => $collection->merchant->mer_pic,
            'mobile' => $collection->merchant->contact_mobile,
            'cost' => $collection->merchant->per_cost,
            'title' => $collection->merchant->title,
            'detail' => $collection->merchant->details,
            'open_time' => $collection->merchant->open_time
        ];
    }
}
