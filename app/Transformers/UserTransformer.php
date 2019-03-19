<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->member_id,
            'name' => $user->username,
            'nickname' => $user->nickname,
            'avatar' => $user->headimgurl,
            'mobile' => $user->mobile,
            'bound_phone' => $user->mobile ? true : false,
            'bound_wechat' => ($user->unionid || $user->openid) ? true : false,
            'created_at' => $user->regtime,
        ];
    }
}
