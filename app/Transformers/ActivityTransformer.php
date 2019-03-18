<?php

namespace App\Transformers;

use App\Models\O2oActivity;
use League\Fractal\TransformerAbstract;

class ActivityTransformer extends TransformerAbstract
{
    public function transform(O2oActivity $activity)
    {
        return [
            'id' => $activity->id,
            'name' => $activity->activity_name
        ];
    }
}
