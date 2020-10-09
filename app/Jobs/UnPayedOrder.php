<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use App\Models\O2oOrder;
use Illuminate\Support\Facades\Log;
use App\Jobs\CloseOrder;

class UnPayedOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cancel_mins;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cancel_mins = 10;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orderModel = new O2oOrder();

        $cancel_point = date('Y-m-d H:i:s', time() - $this->cancel_mins * 60);

        $orderModel->where('pay_result', '1111')->whereDate('tran_time', '<=', $cancel_point)->orderBy('tran_time')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                CloseOrder::dispatch($order);
            }
        });

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
}
