<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\O2oCoupon;
use App\Models\O2oCouponBuy;
use App\Models\O2oOrder;

use App\Transformers\CouponTransformer;
use App\Transformers\CouponBuyTransformer;

use App\Http\Requests\Api\CouponRequest;
use Illuminate\Support\Facades\Log;

class CouponsController extends Controller
{
    public function index(Request $request, O2oCoupon $coupon)
    {
        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;

        $query = $coupon->query();

        if ($merId = $request->mer_id) {
            $query->where('mer_id', $merId);
        }

        if ($cardType = $request->card_type) {
            $query->where('card_type', $cardType);
        }

        $query->where('is_del', 0);
        $query->where('coupon_status', 0);

        $query->recentReplied();
        $coupons = $query->paginate($pageLimit);

        return $this->response->paginator($coupons, new CouponTransformer());
    }

    public function detail(Request $request, O2oCoupon $coupon)
    {

        if (empty($request->pcid)) {
            return $this->errorResponse(404, '优惠券不存在', 1001);
        }

        $couponData = $coupon->where('pcid', $request->pcid)->first();

        if (empty($couponData)) {
            return $this->errorResponse(404, '优惠券不存在', 1002);
        }

        $member_id = $this->user ? $this->user->platform_member_id : 0;

        return $this->response->item($couponData, new CouponTransformer($member_id));
    }

    public function receive(CouponRequest $request, O2oCoupon $coupon, O2oCouponBuy $couponBuy)
    {

        $couponData = $coupon->where('pcid', $request->pcid)->first();

        if (empty($couponData)) {
            return $this->errorResponse(404, '优惠券不存在', 1002);
        }

        if ($couponData->isDated()) {
            return $this->errorResponse(422, '该优惠券已过期', 1003);
        }

        if ($couponData->quantity < 1) {
            return $this->errorResponse(422, '该优惠券库存不足', 1004);
        }

        if ($couponData->get_limit > 0) {
            $reveived = $couponBuy->where([['pcid', $request->pcid], ['platform_member_id', $this->user->platform_member_id]])->count();
            if ($reveived >= $couponData->get_limit) {
                return $this->errorResponse(422, '已超过领取数量限制', 1005);
            }
        }

        do {

            $qrcode = get_qrcode();
            $row = $couponBuy->where('qrcode', $qrcode)->count();

        } while ($row);

        \DB::transaction(function () use ($couponBuy, $couponData, $request, $qrcode) {

            $nowTime = time();
            $nowDateTime = date('Y-m-d H:i:s', $nowTime);

            $couponBuy->pcid = $couponData->pcid;
            $couponBuy->qrcode = $qrcode;
            $couponBuy->cid = $couponData->cid;
            $couponBuy->member_id = $couponBuy->platform_member_id = $this->user->platform_member_id;
            $couponBuy->openid = $this->user->openid;
            $couponBuy->pay_status = '1';
            $couponBuy->buy_status = '1';
            $couponBuy->use_status = '0';
            $couponBuy->createtime = $couponBuy->last_modified = $nowDateTime;

            // 判断优惠券是否需要购买
            if ($couponData->is_buy && $couponData->sale_price > 0) {

                do {
                    $orderNo = get_order_no(strtolower(config('app.name')) . '-');
                    $row = O2oOrder::where('order_no', $orderNo)->count();
                } while ($row);

                $payConfig = config('etonepay', ['mch_id'=>'', 'mer_key'=>'']);

                O2oOrder::create([
                    'order_no' => $orderNo,
                    'mch_id' => $payConfig['mch_id'],
                    'member_id' => $this->user->platform_member_id,
                    'source' => '02',
                    'pay_amount' => $couponData->sale_price * 100,
                    'pay_type' => '03',
                    'scan_pay_type' => '01',
                    'pay_result' => '1111',
                    'pay_info' => json_encode(['memberId'=>$this->user->platform_member_id, 'pcid'=>$couponData->pcid]),
                    'tran_time' => $nowDateTime,
                    'etone_order_id' => $qrcode
                ]);

                $zhusao = [
                    'merchantId' => $payConfig['mch_id'],
                    'merOrderNum' => $orderNo,
                    'tranAmt' => $couponData->sale_price * 100,
                    'sysTraceNum' => $orderNo,
                    'tranDateTime' => date('YmdHis', $nowTime),
                    'notifyUrl' => $payConfig['back_url'] ? $payConfig['back_url'] : url('api/pay/notify'),
                    'merKey' => $payConfig['mch_key']
                ];
                $payMsg = '';
                $payUrl = zhusao($zhusao, $payMsg);

                if (!$payUrl) {
                    return $this->errorResponse(422, $payMsg, 1004);
                }

                $couponBuy->from_order_id = $orderNo;
                $couponBuy->pay_status = '0';
            }

            $couponBuy->save();

            if ($couponData->decreaseQuantity(1) <= 0) {
                return $this->errorResponse(422, '该优惠券库存不足', 1004);
            }
            $couponData->addGrantQuantity(1);
        });

        return $this->response->created();

    }

    public function show(Request $request, O2oCouponBuy $couponBuy)
    {

        if (empty($request->qrcode)) {
            return $this->errorResponse(404, '优惠券不存在', 1001);
        }

        $query = $couponBuy->query();
        $query->with('coupon');
        $query->where([['platform_member_id', $this->user->platform_member_id], ['qrcode', $request->qrcode]]);
        $coupon = $query->first();

        return $this->item($coupon, new CouponBuyTransformer());
    }
}
