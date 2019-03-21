<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\O2oCouponBuy;
use App\Models\O2oMemberCollection;

use App\Transformers\UserTransformer;
use App\Transformers\CouponBuyTransformer;
use App\Transformers\CollectionTransformer;

use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\CollectionRequest;

class UsersController extends Controller
{
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function bind(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            // return $this->response->error('验证码已失效', 422);
            return $this->errorResponse(422, '验证码已失效', 1003);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            // return $this->response->errorUnauthorized('验证码错误');
            return $this->errorResponse(401, '验证码错误', 1003);

        }

        $user = $this->user();

        $user->update(['mobile'=>$request->mobile]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->created();
    }

    public function coupons(Request $request, O2oCouponBuy $coupon)
    {
        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;
        $status = $request->status ? $request->status : 'unused';

        $query = $coupon->query();
        $query->with('coupon');
        $query->where('buy_status', '1');
        $query->where('platform_member_id', $this->user->platform_member_id);

        switch ($status) {
            case 'unused':
                $query->where('use_status', '0');
                break;
            case 'used':
                $query->whereIn('use_status', ['1', '2']);
                break;
            default:
                break;
        }

        $query->recentReplied();
        $coupons = $query->get();

        if ($status == 'dated') {
            $filtered = $coupons->filter(function ($coupon, $key) {
                if ($coupon->coupon->date_type == 'DATE_TYPE_FIX_TIME_RANGE') {
                    $now = date('Y-m-d H:i:s', time());
                    return $coupon->coupon->end_timestamp < $now;
                } else {
                    return strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600 < time();
                }
            });
        } else {
            $filtered = $coupons->filter(function ($coupon, $key) {
                if ($coupon->coupon->date_type == 'DATE_TYPE_FIX_TIME_RANGE') {
                    $now = date('Y-m-d H:i:s', time());
                    return $coupon->coupon->end_timestamp >= $now;
                } else {
                    return strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600 >= time();
                }
            });
        }

        return $this->response->collection($filtered, new CouponBuyTransformer());
    }

    public function fav(CollectionRequest $request, O2oMemberCollection $collection)
    {
        $deleteRows = $collection->where([['mer_id', $request->mer_id], ['platform_member_id', $this->user->platform_member_id]])->delete();
        if ($deleteRows > 0) {
            return $this->response->noContent();
        }

        $collection->platform_member_id = $this->user->platform_member_id;
        $collection->mer_id = $request->mer_id;

        $collection->save();

        $this->response->created();
    }

    public function favs(Request $request, O2oMemberCollection $collection)
    {
        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;
        $query = $collection->query();
        $query->with('merchant');
        $query->where('platform_member_id', $this->user->platform_member_id);
        $query->recentReplied();
        $merchants = $query->paginate($pageLimit);

        return $this->response->paginator($merchants, new CollectionTransformer());
    }
}
