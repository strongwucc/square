<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use App\Models\O2oOrder;
use App\Models\O2oCouponBuy;
use App\Models\O2oCoupon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(O2oOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @param $query_data
     * @return void
     */
    public function handle()
    {
        $query_data = array(
            'accessId' => 'accessId',
            'outTradeNo' => $this->order->order_no,
            'merKey' => 'merKey'
        );
        $query_msg = '';
        $payed = hk_query($query_data, $query_msg);
        if ($payed) {
            $this->order_payed($this->order);
        } else {
            $this->order_cancel($this->order);
        }
    }

    /**
     * 执行失败的任务
     *
     * @param Exception $e
     * @return void
     */
    public function failed(Exception $e) {
        Log::channel('queue')->info('队列任务执行失败：'.print_r($e->getMessage(), true));
    }

    protected function order_payed(O2oOrder $order) {

        DB::beginTransaction();

        $order->pay_result = '0000';

        $orderRes = $order->save();

        if (!$orderRes) {
            DB::rollBack();
            return false;
        }

        // 优惠券
        if ($order->source == '02') {
            $couponBuyModel = new O2oCouponBuy();
            $couponBuyRes = $couponBuyModel->where('from_order_id', $order->order_no)->update(['pay_status'=>'1']);
            if (!$couponBuyRes) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();

        return true;
    }

    protected function order_cancel(O2oOrder $order) {

        DB::beginTransaction();

        $order->pay_result = '8888';

        $orderRes = $order->save();

        if (!$orderRes) {
            DB::rollBack();
            return false;
        }

        // 优惠券
        if ($order->source == '02') {
            $couponBuyModel = new O2oCouponBuy();
            $couponBuyData = $couponBuyModel->where('from_order_id', $order->order_no)->first();
            if ($couponBuyData->coupon) {
                if ($couponBuyData->coupon->decreaseGrantQuantity(1) <= 0) {
                    DB::rollBack();
                    return false;
                }
                $couponBuyData->coupon->addQuantity(1);
            }
        }

        DB::commit();

        return true;
    }
}
