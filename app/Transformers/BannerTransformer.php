<?php

namespace App\Transformers;

use App\Models\O2oBanner;
use League\Fractal\TransformerAbstract;

class BannerTransformer extends TransformerAbstract
{
    public function transform(O2oBanner $banner)
    {
        return [
            'id' => $banner->id,
            'name' => $banner->banner_name,
            'url' => $banner->banner_url,
            'pic' => $banner->banner_pic
        ];
    }
}
