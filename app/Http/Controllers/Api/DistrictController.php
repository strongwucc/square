<?php

namespace App\Http\Controllers\Api;

use App\Models\O2oCouponBuy;
use App\Models\O2oOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Jobs\SendSms;

class DistrictController extends Controller
{
    public function notify(Request $request)
    {
        $notifyData = $request->all();
        $payConfig = config('etonepay', ['mch_id'=>'', 'mer_key'=>'']);
        $notifyData['merKey'] = $payConfig['mch_key'];

        if (is_return_vaild($notifyData) == false) {
            echo 'fail';exit;
        }

        $orderModel = new O2oOrder();
        $couponBuyModel = new O2oCouponBuy();

        $order = $orderModel->where('order_no', $notifyData['merOrderNum'])->first();

        if (!$order) {
            echo 'fail';exit;
        }

        DB::beginTransaction();

        if ($notifyData['respCode'] === '0000') {
            $order->pay_result = '0000';
            $order->remark = $notifyData['channelOrderId'];
        } else {
            $order->pay_result = '9999';
        }

        $orderRes = $order->save();

        if (!$orderRes) {
            DB::rollBack();
            echo 'fail';exit;
        }

        // 优惠券
        if ($order->source == '02') {
            $payInfo = json_decode($order->pay_info, true);
            $couponBuyRes = $couponBuyModel->where('from_order_id', $notifyData['merOrderNum'])->update(['pay_status'=>'1']);
            if (!$couponBuyRes) {
                DB::rollBack();
                echo 'fail';exit;
            }
        }

        DB::commit();

        echo 'success';exit;
    }

    public function hkNotify(Request $request)
    {
        $notifyData = $request->all();

        Log::channel('pay')->info('[海科支付通知报文]：' . print_r($notifyData, true));

        $orderModel = new O2oOrder();
        $couponBuyModel = new O2oCouponBuy();

        $order = $orderModel->where('order_no', $notifyData['out_trade_no'])->first();

        if (!$order) {
            echo 'fail';exit;
        }

        DB::beginTransaction();

        $order->pay_result = '0000';
        $order->remark = $notifyData['trade_no'];

        $orderRes = $order->save();

        if (!$orderRes) {
            DB::rollBack();
            echo 'fail';exit;
        }

        // 优惠券
        if ($order->source == '02') {
            $payInfo = json_decode($order->pay_info, true);
            $couponBuyRes = $couponBuyModel->where('from_order_id', $notifyData['out_trade_no'])->update(['pay_status'=>'1']);
            if (!$couponBuyRes) {
                DB::rollBack();
                echo 'fail';exit;
            }

            // 发送短信
            CloseOrder::dispatch($order);
        }

        DB::commit();

        echo 'success';exit;
    }

    public function info()
    {
        $name = config('trading.name');
        $picture = config('trading.picture');

        $info = ['name' => $name, 'picture' => $picture];

        return $this->response->array($info);
    }
}
