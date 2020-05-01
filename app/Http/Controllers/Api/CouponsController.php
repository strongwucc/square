<?php

namespace App\Http\Controllers\Api;

use App\Models\O2oCouponUser;
use App\Models\O2oMember;
use Illuminate\Http\Request;

use App\Models\O2oCoupon;
use App\Models\O2oCouponBuy;
use App\Models\O2oOrder;

use App\Transformers\CouponTransformer;
use App\Transformers\CouponBuyTransformer;

use App\Http\Requests\Api\CouponRequest;
use Illuminate\Support\Facades\DB;
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
//        $query->where('coupon_status', 0);
        $query->where('end_timestamp', '>=', date('Y-m-d H:i:s'));

        $query->recentReplied();
        $coupons = $query->paginate($pageLimit);

        $member_id = $this->user ? $this->user->platform_member_id : 0;

        return $this->response->paginator($coupons, new CouponTransformer($member_id));
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
            $reveived = $couponBuy->where([['pcid', $request->pcid], ['platform_member_id', $this->user->platform_member_id], ['buy_status', '1'], ['pay_status', '1']])->count();
            if ($reveived >= $couponData->get_limit) {
                return $this->errorResponse(422, '已超过领取数量限制', 1005);
            }
        }

        if ($couponData->day_get_limit > 0) {
            $date_today = date('Y-m-d 00:00:00');
            $today_reveived = $couponBuy->where([['pcid', $request->pcid], ['platform_member_id', $this->user->platform_member_id], ['buy_status', '1'], ['pay_status', '1'], ['createtime', '>=', $date_today]])->count();
            if ($today_reveived >= $couponData->day_get_limit) {
                return $this->errorResponse(422, '已超过当日领取数量限制', 1006);
            }
        }

        do {

            $qrcode = get_qrcode();
            $row = $couponBuy->where('qrcode', $qrcode)->count();

        } while ($row);

        $payUrl = '';

        \DB::transaction(function () use ($couponBuy, $couponData, $request, $qrcode, &$payUrl) {

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
                    'frontUrl' => $request->frontUrl ? $request->frontUrl : '',
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

        return $this->response->array(['payUrl' => $payUrl]);

    }

    public function show(Request $request, O2oCouponBuy $couponBuy)
    {
        $where = [['platform_member_id', $this->user->platform_member_id]];

        if (!empty($request->qrcode)) {
            array_push($where, ['qrcode', $request->qrcode]);
        }

        $query = $couponBuy->query();
        $query->with('coupon');
        $query->with('order');
        $query->where($where);
        $query->orderBy('createtime', 'desc');
        $coupon = $query->first();

        if (!$coupon) {
            return $this->errorResponse(404, '优惠券不存在', 1001);
        }

        return $this->item($coupon, new CouponBuyTransformer());
    }

    public function couponsByOpenid(Request $request, O2oCouponBuy $couponBuy)
    {
        $openid = $request->openid ? $request->openid : '';

        if (!$openid) {
            return $this->errorResponse(422, 'Bad Request', 1003);
        }

        $member_model = new O2oMember();
        $member = $member_model->where('openid', $openid)->first();

        if (!$member) {
            return $this->errorResponse(404, '用户不存在', 1003);
        }

        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;
        $status = $request->status ? $request->status : 'unused';

        $query = $couponBuy->query();
        $query->with('coupon');
        $query->where('buy_status', '1');
        $query->where('pay_status', '1');
        $query->where('platform_member_id', $member->platform_member_id);

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
                if (!$coupon->coupon) {
                    return false;
                } elseif ($coupon->coupon->date_type == 'DATE_TYPE_FIX_TIME_RANGE') {
                    $now = date('Y-m-d H:i:s', time());
                    return $coupon->coupon->end_timestamp < $now;
                } else {
                    return strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600 < time();
                }
            });
        } else {
            $filtered = $coupons->filter(function ($coupon, $key) {
                if (!$coupon->coupon) {
                    return false;
                } elseif ($coupon->coupon->date_type == 'DATE_TYPE_FIX_TIME_RANGE') {
                    $now = date('Y-m-d H:i:s', time());
                    return $coupon->coupon->end_timestamp >= $now;
                } else {
                    return strtotime($coupon->createtime) + ($coupon->coupon->fixed_begin_term + $coupon->coupon->fixed_term) * 24 * 3600 >= time();
                }
            });
        }

        return $this->response->collection($filtered, new CouponBuyTransformer());
    }

    public function couponByOpenid(Request $request, O2oCouponBuy $couponBuy)
    {
        $openid = $request->openid ? $request->openid : '';
        $qrcode = $request->qrcode ? $request->qrcode : '';

        if (!$openid || !$qrcode) {
            return $this->errorResponse(422, 'Bad Request', 1003);
        }

        $member_model = new O2oMember();
        $member = $member_model->where('openid', $openid)->first();

        if (!$member) {
            return $this->errorResponse(404, '用户不存在', 1003);
        }

        $query = $couponBuy->query();
        $query->with('coupon');
        $query->where('qrcode', $qrcode);
        $query->where('platform_member_id', $member->platform_member_id);

        $query->recentReplied();
        $coupon = $query->first();

        if (!$coupon) {
            return $this->errorResponse(404, '优惠券不存在', 1004);
        }

        return $this->response->item($coupon, new CouponBuyTransformer());
    }

    public function writeOff(Request $request, O2oCouponBuy $couponBuy)
    {
        $openid = $request->openid ? $request->openid : '';
        $qrcode = $request->qrcode ? $request->qrcode : '';
        $order_no = $request->order_no ? $request->order_no : '';
        $order_amt = $request->order_amt ? $request->order_amt : '';
        $mer_id = $request->mer_id ? $request->mer_id : '';

        $request_time = time();

        if (!$qrcode || !$mer_id) {
            return $this->errorResponse(422, 'Bad Request', 1003);
        }

        $order_amt = floatval($order_amt);

        $coupon_data = $couponBuy->where('qrcode', $qrcode)
            ->where('use_status', '0')
            ->first();

        if (!$coupon_data) {
            return $this->errorResponse(404, '优惠券不存在', 1002);
        }

        // 检查适用商户
        if (!empty($coupon_data->coupon->mer_id)) {
            if (!in_array($mer_id, $coupon_data->coupon->mer_id)) {
                return $this->errorResponse(422, '不适用该商户', 1008);
            }
        }

        // 检查有效期
        $end_timestamp = strtotime($coupon_data->coupon->end_timestamp);
        if ($coupon_data->coupon->fixed_begin_term > 0) {
            $end_time = strtotime($coupon_data->createtime) + $coupon_data->coupon->fixed_begin_term * 24 * 3600;
            $end_time = $end_timestamp >= $end_time ? $end_time : $end_timestamp;
        } else {
            $end_time = $end_timestamp;
        }

        $begin_timestamp = strtotime($coupon_data->coupon->begin_timestamp);

        if ($request_time < $begin_timestamp) {
            return $this->errorResponse(422, '该优惠券未生效', 1004);
        }

        if ($request_time >= $end_time) {
            return $this->errorResponse(422, '该优惠券已过期', 1005);
        }

        // 检查可用时间段
        $now_day = date('j');
        $now_week = date('w');
        $limit_time_checked = true;
        if ($coupon_data->coupon->limit_time_type == '0') {
            if (($coupon_data->coupon->days && !in_array($now_day, $coupon_data->coupon->days)) && ($coupon_data->coupon->weeks && !in_array($now_week, $coupon_data->coupon->weeks))) {
                $limit_time_checked = false;
            }
            if (($coupon_data->coupon->days && !in_array($now_day, $coupon_data->coupon->days)) && !$coupon_data->coupon->weeks) {
                $limit_time_checked = false;
            }
            if (!$coupon_data->coupon->days && ($coupon_data->coupon->weeks && !in_array($now_week, $coupon_data->coupon->weeks))) {
                $limit_time_checked = false;
            }
        } else {
            if (($coupon_data->coupon->days && in_array($now_day, $coupon_data->coupon->days)) || ($coupon_data->coupon->weeks && in_array($now_week, $coupon_data->coupon->weeks))) {
                $limit_time_checked = false;
            }
        }

        if (!$limit_time_checked) {
            return $this->errorResponse(422, '不在可用时间段', 1008);
        }

        // 计算优惠金额
        $card_type = $coupon_data->coupon->card_type;
        $order_pay_amt = $order_amt;
        $order_derate_amt = 0;

        if ($card_type == 'DISCOUNT') {
            $order_derate_amt = $order_amt * (100 - $coupon_data->coupon->discount) / 100;
            $order_pay_amt = bcsub($order_amt, $order_derate_amt, 2);
        } elseif ($card_type == 'CASH') {
            $order_derate_amt = floatval($coupon_data->coupon->reduce_cost);
            $order_pay_amt = bcsub($order_amt, $order_derate_amt, 2);
        } elseif ($card_type == 'FULL_REDUCTION') {
            if ($order_amt < $coupon_data->coupon->least_cost) {
                return $this->errorResponse(422, '未达到最低消费金额', 1005);
            }
            $order_derate_amt = floatval($coupon_data->coupon->reduce_cost);
            $order_pay_amt = bcsub($order_amt, $order_derate_amt, 2);
        }

        DB::beginTransaction();

        $record_model = new O2oCouponUser();

        $record_model->pcid = $coupon_data->pcid;
        $record_model->qrcode = $qrcode;
        $record_model->order_no = $order_no;
        $record_model->order_amt = $order_amt;
        $record_model->order_pay_amt = $order_pay_amt;
        $record_model->order_derate_amt = $order_derate_amt;
        $record_model->mer_id = $mer_id;
        $record_model->member_id = '';
        $record_model->createtime = date('Y-m-d H:i:s', $request_time);

        if (!$record_model->save()) {
            DB::rollBack();
            return $this->errorResponse(422, '核销失败', 1006);
        }

        $update_res = $couponBuy->where('qrcode', $qrcode)
            ->where('use_status', '0')
            ->update(['order_id' => $order_no, 'use_status' => '1', 'last_modified' => date('Y-m-d H:i:s', $request_time)]);

        if (!$update_res) {
            DB::rollBack();
            return $this->errorResponse(422, '核销失败', 1007);
        }

        DB::commit();

        $response_data = array('pay_amt'=>$order_pay_amt,'derate_amt'=>$order_derate_amt,'card_type'=>$card_type);
        return $this->response->array($response_data);
        #return $this->response->noContent();
    }

}
