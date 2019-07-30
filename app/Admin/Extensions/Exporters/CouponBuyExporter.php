<?php
/**
 * Created by PhpStorm.
 * User: wuchuanchuan
 * Date: 2019/6/26
 * Time: 4:55 PM
 */
namespace App\Admin\Extensions\Exporters;

use App\Models\O2oCoupon;
use App\Models\O2oCouponUser;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class CouponBuyExporter extends ExcelExporter implements WithMapping
{
    protected $fileName = '优惠券核销列表.xlsx';

    protected $columns = [
        'pcid'                  => '优惠券编号',
        'order_id'              => '优惠券名称',
        'qrcode'                => '优惠券核销码',
        'openid'                => '用户openid',
        'from_order_id'         => '核销时间',
        'member_id'             => '核销商户',
        'use_status'            => '交易金额',
        'cashier_id'            => '优惠金额',
        'pay_status'            => '实付金额'
    ];

    public function map($row): array
    {
        return [
            $row->pcid,
            $this->getTitle($row->pcid),
            $row->qrcode,
            $row->openid,
            $this->getUseTime($row->pcid, $row->qrcode),
            $this->getMerId($row->pcid, $row->qrcode),
            $this->getOrderAmt($row->pcid, $row->qrcode),
            $this->getDerateAmt($row->pcid, $row->qrcode),
            $this->getPayAmt($row->pcid, $row->qrcode),
//            $this->useInfo->createtime,
//            $this->useInfo->mer_id,
//            $this->useInfo->order_amt,
//            $this->useInfo->order_derate_amt,
//            $this->useInfo->order_pay_amt
        ];
    }

    public function getTitle($key)
    {
        $coupon = O2oCoupon::where('pcid', $key)->first();
        return $coupon ? $coupon->title : '';
    }

    public function getUseTime($pcid, $qrcode)
    {
        $coupon = O2oCouponUser::where([['pcid', '=', $pcid], ['qrcode', '=', $qrcode]])->first();
        return $coupon ? $coupon->createtime : '';
    }

    public function getMerId($pcid, $qrcode)
    {
        $coupon = O2oCouponUser::where([['pcid', '=', $pcid], ['qrcode', '=', $qrcode]])->first();
        return $coupon ? $coupon->mer_id : '';
    }

    public function getOrderAmt($pcid, $qrcode)
    {
        $coupon = O2oCouponUser::where([['pcid', '=', $pcid], ['qrcode', '=', $qrcode]])->first();
        return $coupon ? $coupon->order_amt : '';
    }

    public function getDerateAmt($pcid, $qrcode)
    {
        $coupon = O2oCouponUser::where([['pcid', '=', $pcid], ['qrcode', '=', $qrcode]])->first();
        return $coupon ? $coupon->order_derate_amt : '';
    }

    public function getPayAmt($pcid, $qrcode)
    {
        $coupon = O2oCouponUser::where([['pcid', '=', $pcid], ['qrcode', '=', $qrcode]])->first();
        return $coupon ? $coupon->order_pay_amt : '';
    }
}