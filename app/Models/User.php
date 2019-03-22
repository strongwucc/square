<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const CREATED_AT = 'regtime';
    const UPDATED_AT = 'regtime';

    protected $table = 'o2o_member';
    protected $primaryKey = 'member_id';

    public $timestamps = false;
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'mobile', 'password', 'nickname', 'headimgurl', 'province', 'country', 'openid', 'unionid', 'regtime', 'platform_member_id', 'source'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    // Rest omitted for brevity

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getOrderTotal($memberId, $status, $page=1, $pageLimit=10)
    {
        $o2oOrdersSql = "SELECT COUNT(order_no) AS order_total FROM mch_etongpay_order WHERE member_id = " . $memberId;
        $b2cOrdersSql = "SELECT COUNT(order_id) AS order_total FROM sdb_b2c_orders WHERE platform_member_id = " . $memberId;

        if ($status == 'unpayed') {
            $o2oOrdersSql .= " AND pay_result IN ('1111', '8888')";
            $b2cOrdersSql .= " AND pay_result = '0' AND status = 'active'";
        }

        if ($status == 'payed') {
            $o2oOrdersSql .= " AND pay_result = '0000'";
            $b2cOrdersSql .= " AND pay_result = '1' AND status = 'active'";
        }

        if ($status == 'dead') {
            $b2cOrdersSql .= " AND status = 'dead'";
        }

        $firstO2oOrders = $status == 'dead' ? [] : \DB::select($o2oOrdersSql);
        $firstB2cOrders = \DB::select($b2cOrdersSql);

        $total = 0;

        if (!empty($firstO2oOrders)) {
            $firstO2oOrder = $firstO2oOrders[0];
            $total += $firstO2oOrder->order_total;
        }

        if (!empty($firstB2cOrders)) {
            $firstB2cOrder = $firstB2cOrders[0];
            $total += $firstB2cOrder->order_total;
        }

        $pagination = [
            'total' => $total,
            'count' => $pageLimit,
            'per_page' => $pageLimit,
            'current_page' => $page,
            'total_page' => ceil($total / $pageLimit)
        ];

        return $pagination;
    }

    public function getOrders($memberId, $status, $page=1, $pageLimit=10)
    {
        // 总偏移量
        $totalOffset = ($page - 1) * $pageLimit;

        // 平均偏移量
        $avgOffset = $totalOffset / 2;

        // 首次订单数据查询
        $o2oOrdersSql = "SELECT order_no,source,pay_amount,pay_result,unix_timestamp(tran_time) AS tran_time,'o2o' AS platform FROM mch_etongpay_order WHERE member_id = " . $memberId;
        $b2cOrdersSql = "SELECT order_id AS order_no,source,total_amount AS pay_amount,pay_status AS pay_result,createtime AS tran_time,'b2c' AS platform FROM sdb_b2c_orders WHERE platform_member_id = " . $memberId;

        if ($status == 'unpayed') {
            $o2oOrdersSql .= " AND pay_result IN ('1111', '8888')";
            $b2cOrdersSql .= " AND pay_result = '0' AND status = 'active'";
        }

        if ($status == 'payed') {
            $o2oOrdersSql .= " AND pay_result = '0000'";
            $b2cOrdersSql .= " AND pay_result = '1' AND status = 'active'";
        }

        if ($status == 'dead') {
            $b2cOrdersSql .= " AND status = 'dead'";
        }

        $firstO2oOrdersSql = $o2oOrdersSql . " ORDER BY tran_time DESC LIMIT " . $avgOffset . ", " . $pageLimit;
        $firstOb2cOrdersSql = $b2cOrdersSql . " ORDER BY tran_time DESC LIMIT " . $avgOffset . ", " . $pageLimit;

        $firstO2oOrders = $status == 'dead' ? [] : \DB::select($firstO2oOrdersSql);
        $firstB2cOrders = \DB::select($firstOb2cOrdersSql);

        // 找到全部数据的 tran_time 的最大值
        $tran_time_max = 0;
        if (isset($firstO2oOrders[0])) {
            $firstO2oOrder = $firstO2oOrders[0];
            $tran_time_max = $firstO2oOrder->tran_time;
        }

        if (isset($firstB2cOrders[0])) {
            $firstB2cOrder = $firstB2cOrders[0];
            if ($firstB2cOrder->tran_time > $tran_time_max) {
                $tran_time_max = $firstB2cOrder->tran_time;
            }
        }

        if ($tran_time_max == 0) {
            return [];
        }

        // print_r(date('Y-m-d H:i:s', $tran_time_max));exit;

        // 分别找到 tran_time 的最小值
        $o2o_tran_time_min = 0;
        if (count($firstO2oOrders) > 0) {
            $o2oEndOrder = end($firstO2oOrders);
            $o2o_tran_time_min = $o2oEndOrder->tran_time;
        }

        $b2c_tran_time_min = 0;
        if (count($firstB2cOrders) > 0) {
            $b2cEndOrder = end($firstB2cOrders);
            $b2c_tran_time_min = $b2cEndOrder->tran_time;
        }

        // 第二次查询订单数据
        $secondO2oOrdersSql = $o2oOrdersSql . " AND unix_timestamp(tran_time) BETWEEN " . $o2o_tran_time_min . " AND " . $tran_time_max . " ORDER BY tran_time DESC";
        $secondB2cOrdersSql = $b2cOrdersSql . " AND createtime BETWEEN " . $b2c_tran_time_min . " AND " . $tran_time_max . " ORDER BY tran_time DESC";

        $secondO2oOrders = $status == 'dead' ? [] : \DB::select($secondO2oOrdersSql);
        $secondB2cOrders = \DB::select($secondB2cOrdersSql);

        // 找到全局的 offset，即 $tran_time_max 在全局的 offset
        $finalOffset = $totalOffset - (count($secondB2cOrders) + count($secondO2oOrders) - count($firstB2cOrders) - count($firstO2oOrders));

        // print_r($finalOffset);exit;

        // print_r($secondO2oOrders);exit;

        // 合并第二次数据并且排序
        $mergeOrders = array_merge($secondO2oOrders, $secondB2cOrders);

        usort($mergeOrders, 'timestamp_cmp');

        // 找到 totalOffset 在合并数组中的 offset
        $arrOffsetIndex = 0;

        foreach ($mergeOrders as $mergeIndex => $mergeOrder) {
            if ($mergeOrder->tran_time == $tran_time_max) {
                $arrOffsetIndex = $mergeIndex;
                break;
            }
        }

        $mergeOffset = $arrOffsetIndex + $totalOffset - $finalOffset;

        // print_r($arrOffsetIndex);exit;
        // print_r($mergeOrders);exit;

        $orders = array_slice($mergeOrders, $mergeOffset, $pageLimit);

        return $orders;

    }
}
