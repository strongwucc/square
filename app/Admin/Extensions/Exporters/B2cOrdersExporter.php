<?php
/**
 * Created by PhpStorm.
 * User: wuchuanchuan
 * Date: 2019/6/26
 * Time: 4:55 PM
 */
namespace App\Admin\Extensions\Exporters;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class B2cOrdersExporter extends ExcelExporter implements WithMapping
{
    protected $fileName = '订单列表.xlsx';

    protected $columns = [
        'seller_order_id'       => '订单号',
        'total_amount'          => '订单金额',
        'pay_status'            => '付款状态',
        'createtime'            => '下单时间',
        'payment'               => '支付方式',
        'platform_member_id'    => '会员ID',
        'confirm_status'        => '确认状态',
        'confirm_time'          => '确认时间',
        'pickself_status'       => '自提状态',
        'pickself_time'         => '自提时间',
        'qrcode'                => '核销码',
        'status'                => '订单状态',
        'cost_item'             => '订单商品总价格',
        'is_tax'                => '是否开具发票',
        'tax_type'              => '发票类型',
        'tax_content'           => '发票内容',
        'cost_tax'              => '发票税率',
        'tax_company'           => '发票抬头',
        'is_protect'            => '是否有保价费',
        'cost_protect'          => '保价费',
        'cost_payment'          => '支付费用',
        'score_u'               => '订单使用积分',
        'score_g'               => '订单获得积分',
        'discount'              => '订单减免',
        'pmt_goods'             => '商品促销优惠',
        'pmt_order'             => '订单促销优惠',
        'payed'                 => '订单支付金额',
        'source'                => '订单来源',
        'merchant_bn'           => '商户号'
    ];

    public function map($row): array
    {
        return [
            $row->seller_order_id,
            $row->total_amount,
            $this->getPayStatus($row->pay_status),
            $this->getFormatDate($row->createtime),
            $this->getPayment($row->payment),
            $row->platform_member_id,
            $this->getConfirmStatus($row->confirm_status),
            $this->getFormatDate($row->confirm_time),
            $this->getPickStatus($row->pickself_status),
            $this->getFormatDate($row->pickself_time),
            $row->qrcode,
            $this->getStatus($row->status),
            $this->getFormatPrice($row->cost_item),
            $this->getFalseOrTrue($row->is_tax),
            $this->getTaxType($row->tax_type),
            $row->tax_content,
            $row->cost_tax,
            $row->tax_company,
            $this->getFalseOrTrue($row->is_protect),
            $this->getFormatPrice($row->cost_protect),
            $this->getFormatPrice($row->cost_payment),
            $this->getFormatPrice($row->score_u),
            $this->getFormatPrice($row->score_g),
            $this->getFormatPrice($row->discount),
            $this->getFormatPrice($row->pmt_goods),
            $this->getFormatPrice($row->pmt_order),
            $this->getFormatPrice($row->payed),
            $this->getFormatPrice($row->source),
            $this->getFormatPrice($row->merchant_bn)
        ];
    }

    public function getPayStatus($key)
    {
        $maps = [
            '0'    => '未支付',
            '1'    => '已支付',
            '2'    => '已付款至担保方',
            '3'    => '部分付款',
            '4'    => '部分退款',
            '5'    => '全额退款'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getPayment($key)
    {
        $maps = [
            'offline'           => '现金支付',
            'deposit'           => '会员卡支付',
            'alipaynative'      => '支付宝支付',
            'unionpaynative'    => '银联支付',
            'wxpaynative'       => '微信支付',
            'cardpay'           => '刷卡支付',
            'yktpay'            => '一卡通支付',
            '-1'                => '优惠券抵扣'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getConfirmStatus($key)
    {
        $maps = [
            '0'    => '未接单',
            '1'    => '已接单',
            '2'    => '已拒绝',
            '3'    => '已完成'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getPickStatus($key)
    {
        $maps = [
            '0'    => '未自提',
            '1'    => '已自提'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getStatus($key)
    {
        $maps = [
            'active'    => '活动订单',
            'dead'      => '已作废',
            'finished'  => '已完成'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getFalseOrTrue($key)
    {
        $maps = [
            'false'    => '否',
            'true'    => '是'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getTaxType($key)
    {
        $maps = [
            'personal'    => '个人',
            'company'    => '公司'
        ];

        return isset($maps[$key]) ? $maps[$key] : '-';
    }

    public function getFormatPrice($price)
    {
        return number_format(floatval($price), 2, '.', '');
    }

    public function getFormatDate($date)
    {
        return date('Y-m-d H:i:s', $date);
    }
}