<?php

namespace App\Transformers;

use App\Models\O2oMerchant;
use App\Models\O2oMemberCollection;
use League\Fractal\TransformerAbstract;
use App\Transformers\CouponTransformer;

class MerchantTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['coupons'];
    protected $member_id = 0;

    public function __construct($member_id = 0)
    {
        // parent::__construct();
        $this->member_id = $member_id;
    }

    public function transform(O2oMerchant $merchant)
    {

        $isFav = 0;
        if ($this->member_id) {
            $isFav = O2oMemberCollection::where([['mer_id', $merchant->mer_id], ['platform_member_id', $this->member_id]])->count();
        }

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
            'open_time' => $merchant->open_time,
            'is_fav' => $isFav
        ];
    }

    public function includeCoupons(O2oMerchant $merchant)
    {
        $coupons = [];

        foreach ($merchant->coupons as $key => $value) {
            if ($value['is_del'] == 0) {
                array_push($coupons, $value);
            }
        }

        return $this->collection($coupons, new CouponTransformer($this->member_id));
    }
}
