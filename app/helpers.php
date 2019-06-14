<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function sms_rsa_sign($str){
    $priKey = env('SMS_PRIVATE_KEY');
    $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
        wordwrap($priKey, 64, "\n", true) .
        "\n-----END RSA PRIVATE KEY-----";
    openssl_sign($str, $sign, $res);
    return base64_encode($sign);
}

function sms_post($url, $post_data)
{
    $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
    if(0 === strpos(strtolower($url), 'https')) {
    　　url_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
    　　curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
    }
    curl_setopt($ch, CURLOPT_POST, TRUE);
    $res_json = json_encode($post_data);
    // $res_json = @preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($post_data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $res_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $rtn = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
    curl_close($ch);
    return $rtn;
}

/**
 * 调用易通短信接口
 */
function send_msg($mobile, $content){
    $url = env('SMS_URL');
    $str = 'messageContent='.$content.'&messageNumber='.$mobile.'&key='.env('SMS_KEY');
    $sign = sms_rsa_sign($str);
    $arr = array('messageNumber'=>$mobile,'messageContent'=>$content,'sign'=>$sign);
    $redata = sms_post($url, $arr);
    return json_decode($redata,true);
}

function get_member_id()
{
    return time() . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT) ;
}

function get_qrcode()
{
    return '01' . date('YmdHis', time()) . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT) ;
}

function get_order_no($prefix)
{
    return $prefix . date('YmdHis', time()) . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT) ;
}

function timestamp_cmp($a, $b)
{
    return $b->tran_time - $a->tran_time;
}

function etonepay_post($url, $post_data)
{
    ob_start();
    $o = "";
    foreach ($post_data as $k => $v)
    {
        if ($k == 'authCode') {
            $o .= "$k=" . urlencode($v) . "&";
        } else {
            $o .= "$k=" . $v . "&";
        }
    }
    $post_data = substr($o, 0, -1);
    #echo $post_data;exit;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_exec($ch);
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
}

function zhusao($sdf = [], &$msg)
{
    $post_data = [
        'version' => '1.0.0',
        'transCode' => '8888',
        'merchantId' => $sdf['merchantId'],
        'merOrderNum' => $sdf['merOrderNum'],
        'bussId' => env('ETONEPAY_BANK_ID'),
        'tranAmt' => $sdf['tranAmt'],
        'sysTraceNum' => $sdf['sysTraceNum'],
        'tranDateTime' => $sdf['tranDateTime'],
        'currencyType' => '156',
        'merURL' => $sdf['frontUrl'] ? $sdf['frontUrl'] : $sdf['notifyUrl'],
        'backURL' => $sdf['notifyUrl'],
        'orderInfo' => bin2hex('消费交易'),
        'userId' => '',
        'bankId' => '888880600002900',
        'stlmId' => '',
        'entryType' => '1',
        'authCode' => '',
        'activeTime' => '',
        'sub_appid' => '',
        'sub_openid' => '',
        'channel' => '',
        'payPage' => 'false',
        'attach' => '',
        'reserver1' => '',
        'reserver2' => '',
        'reserver3' => '',
        'reserver4' => ''
    ];

    $merKey = $sdf['merKey'];
    $strSign = $post_data['version'].'|'.$post_data['transCode'].'|'.$post_data['merchantId'].'|'.$post_data['merOrderNum'].'|'.$post_data['bussId'].'|'.$post_data['tranAmt'].'|'.$post_data['sysTraceNum'].'|'.$post_data['tranDateTime'].'|'.$post_data['currencyType'].'|'.$post_data['merURL'].'|'.$post_data['backURL'].'|'.$post_data['orderInfo'].'|'.$post_data['userId'];
    $strSign .= $merKey;

    Log::channel('pay')->info('[主扫支付请求签名串]：' . $strSign);

    $post_data['signValue'] = md5($strSign);
    Log::channel('pay')->info('[主扫支付请求报文]：' . print_r($post_data, true));
    $post_res = etonepay_post(env('ETONEPAY_SUBMIT_URL'), $post_data);
    Log::channel('pay')->info('[主扫支付响应报文]：' . $post_res);

    parse_str($post_res, $post_arr);
    if(isset($post_arr['respCode']) && $post_arr['respCode'] == '0000'){
        return $post_arr['codeUrl'];
    }else{
        $msg = !empty($post_arr['respMsg']) ? $post_arr['respMsg'] : '支付失败';
        return false;
    }

}

function is_return_vaild($arr)
{
    $mer_key = $arr['merKey'];
    $str = $arr['transCode'].'|'.$arr['merchantId'].'|'.$arr['respCode'].'|'.$arr['sysTraceNum'].'|'.$arr['merOrderNum'].'|'.$arr['orderId'].'|'.$arr['bussId'].'|'.$arr['tranAmt'].'|'.$arr['orderAmt'].'|'.$arr['bankFeeAmt'].'|'.$arr['integralAmt'].'|'.$arr['vaAmt'].'|'.$arr['bankAmt'].'|'.$arr['bankId'].'|'.$arr['integralSeq'].'|'.$arr['vaSeq'].'|'.$arr['bankSeq'].'|'.$arr['tranDateTime'].'|'.$arr['payMentTime'].'|'.$arr['settleDate'].'|'.$arr['currencyType'].'|'.$arr['orderInfo'].'|'.$arr['userId'].$mer_key;
    $sign = md5($str);
    if($sign == $arr['signValue']){
        return true;
    }
    return false;
}
