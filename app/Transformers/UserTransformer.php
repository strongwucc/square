<?php

namespace App\Transformers;

use App\Models\O2oCouponBuy;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $coupon_count = O2oCouponBuy::where([['member_id', $user->platform_member_id], ['buy_status', '1'], ['pay_status', '1']])->count();

        return [
            'id' => $user->member_id,
            'member_id' => $user->platform_member_id,
            'name' => $user->username,
            'nickname' => $user->nickname,
            'avatar' => $user->headimgurl,
            'mobile' => $user->mobile,
            'point' => $user->point,
            'coupon_sum' => $coupon_count,
            'advance' => $user->advance,
            'bound_phone' => $user->mobile ? true : false,
            'bound_wechat' => ($user->unionid || $user->openid) ? true : false,
            'created_at' => $user->regtime,
        ];
    }
}
