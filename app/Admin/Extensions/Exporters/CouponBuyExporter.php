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
use App\Models\O2oOrder;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class CouponBuyExporter extends ExcelExporter implements WithMapping
{
    protected $fileName = '优惠券核销列表.xlsx';

    protected $columns = [
        'pcid'                  => '优惠券编号',
        'order_id'              => '优惠券名称',
        'qrcode'                => '优惠券核销码',
        'createtime'            => '领取时间',
//        'openid'                => '用户openid',
//        'from_order_id'         => '核销时间',
//        'member_id'             => '核销商户',
        'use_status'            => '使用状态',
//        'cashier_id'            => '优惠金额',
//        'pay_status'            => '实付金额'
        'from_order_id'         => '烟草专卖证号',
        'pay_status'            => '联系人手机号',
    ];

    public function map($row): array
    {
        return [
            ' '.$row->pcid,
            $this->getTitle($row->pcid),
            ' '.$row->qrcode,
//            $row->openid,
            $row->createtime,
            $this->getUseStatus($row->use_status),
            ' '.$this->getCertNo($row->from_order_id),
            ' '.$this->getBuyMobile($row->from_order_id),
//            $this->getUseTime($row->pcid, $row->qrcode),
//            $this->getMerId($row->pcid, $row->qrcode),
//            $this->getOrderAmt($row->pcid, $row->qrcode),
//            $this->getDerateAmt($row->pcid, $row->qrcode),
//            $this->getPayAmt($row->pcid, $row->qrcode),
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

    public function getUseStatus($status)
    {
        $maps = [
            '0' => '未使用',
            '1' => '已使用',
            '2' => '已冻结'
        ];

        return isset($maps[$status]) ? $maps[$status] : '';
    }

    public function getCertNo($from_order_id) {
        $order = O2oOrder::where('order_no', $from_order_id)->first();
        return $order && $order->cert_no ? $order->cert_no : '';
    }

    public function getBuyMobile($from_order_id) {
        $order = O2oOrder::where('order_no', $from_order_id)->first();
        return $order && $order->buy_mobile ? $order->buy_mobile : '';
    }

    public function getMerId($pcid, $qrcode)
    {
        $coupon = O2oCouponUser::where([['pcid', '=', $pcid], ['qrcode', '=', $qrcode]])->first();
        return $coupon && $coupon->merchant ? $coupon->merchant->mer_name : '';
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