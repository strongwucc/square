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

function sms_get($url)
{
    $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
    if(0 === strpos(strtolower($url), 'https')) {
        　　url_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
        　　curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $rtn = curl_exec($ch);
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

/**
 * 调用亚联通短信接口
 */
function ylt_sms_send($mobile, $content){

    $url = env('YLT_SMS_URL');
    $key = env('YLT_SMS_KEY');
    $appId = env('YLT_SMS_APPID');

    $timestamp = date('YmdHis');
    $sign = md5($key.$timestamp);

    $url .= '?appId='.$appId.'&timestamp='.$timestamp.'&sign='.$sign.'&mobiles='.$mobile.'&content='.urlencode($content);
    $arr = array();
    $redata = sms_post($url, $arr);
    return json_decode($redata,true);
}

/**
 * 调用海科短信接口
 */
function hk_sms_send($mobile, $content){

    $url = env('HK_SMS_URL');
    $un = env('HK_SMS_UN');
    $pwd = env('HK_SMS_PWD');

    $url .= '?un='.$un.'&pwd='.$pwd.'&mobile='.$mobile.'&msg='.urlencode($content);
    $arr = array();
    Log::channel('sms')->info('[海科短信接口请求报文]：' . print_r($url, true));
    $redata = sms_get($url);
    Log::channel('sms')->info('[海科短信接口响应报文]：' . print_r($redata, true));
    return json_decode($redata,true);
}

function get_member_id()
{
    return time() . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT) ;
}

function get_qrcode()
{
    return '01' . date('YmdHis', time()) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) ;
}

function get_order_no($prefix)
{
    return $prefix . date('YmdHis', time()) . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT) ;
}

function get_type_code($now_time)
{
    return date('ynjG', $now_time) . intval(date('i', $now_time)) . intval(date('s', $now_time)) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
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

function post_json($url,$data) {

    $data_string = json_encode($data);

    $headers = array(
        "Content-type: application/json;charset=UTF-8",
        "Accept: application/json",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        'Content-Length: ' . strlen($data_string)
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
    if(0 === strpos(strtolower($url), 'https')) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
    }
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $return = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
    curl_close($ch);

    return $return;
}

//对emoji表情转义
function emoji_encode($str){
    $strEncode = '';

    $length = mb_strlen($str,'utf-8');

    for ($i=0; $i < $length; $i++) {
        $_tmpStr = mb_substr($str,$i,1,'utf-8');
        if(strlen($_tmpStr) >= 4){
            $strEncode .= '[[EMOJI:'.rawurlencode($_tmpStr).']]';
        }else{
            $strEncode .= $_tmpStr;
        }
    }

    return $strEncode;
}

//对emoji表情转反义
function emoji_decode($str){
    $strDecode = preg_replace_callback('|\[\[EMOJI:(.*?)\]\]|', function($matches){
        return rawurldecode($matches[1]);
    }, $str);
    return $strDecode;
}

/**
 * 海科支付
 */
function hkpay($data, &$msg) {

    $pay_data = array(
        'accessid' => $data['accessId'],
        'merch_no' => $data['merchNo'],
        'out_trade_no' => $data['orderNo'],
        'total_amount' => $data['totalAmount'],
        'appid' => $data['appId'],
        'openid' => $data['openId'],
        'notify_url' => $data['notifyUrl']
    );

    $sign = makeSign($pay_data, $data['merKey']);
    $pay_data['sign'] = $sign;

    Log::channel('pay')->info('[海科支付请求报文]：' . print_r($pay_data, true));
    $pay_res = post_json(env('HKPAY_SUBMIT_URL'), $pay_data);
    Log::channel('pay')->info('[海科支付响应报文]：' . $pay_res);

    $res_data = json_decode($pay_res, true);

    $native_obj = array(
        'appId' => isset($res_data['appid']) ? $res_data['appid'] : '',
        'package' => isset($res_data['package']) ? $res_data['package'] : '',
        'timeStamp' => isset($res_data['timestamp']) ? $res_data['timestamp'] : '',
        'nonceStr' => isset($res_data['noncestr']) ? $res_data['noncestr'] : '',
        'paySign' => isset($res_data['paysign']) ? $res_data['paysign'] : '',
        'signType' => isset($res_data['signtype']) ? $res_data['signtype'] : '',
    );

    return $native_obj;

}

function hk_query($data, &$msg){

    $query_data = array(
        'accessid' => $data['accessId'],
        'out_trade_no' => $data['outTradeNo']
    );

    $query_data['sign'] = makeSign($query_data, $data['merKey']);
    Log::channel('pay')->info('[海科交易查询请求报文]：' . print_r($query_data, true));
    $query_res = post_json(env('HKPAY_QUERY_URL'), $query_data);
    Log::channel('pay')->info('[海科交易查询响应报文]：' . print_r($query_res, true));
    $res_data = json_decode($query_res,true);
    if($res_data['return_code'] == '10000' && $res_data['trade_status'] == '1'){
        return true;
    }
    return false;
}

function yancao_query($data, &$msg){

    $api_key = env('YANCAO_API_KEY');
    $api_url = env('YANCAO_API_URL');

    $query_data = array(
        'liceId' => $data['liceId'],
        'signature' => md5($data['liceId'] . $api_key)
    );

    Log::channel('api')->info('[烟草证号查询请求报文]：' . print_r($query_data, true));
    $query_res = post_json($api_url, $query_data);
    Log::channel('api')->info('[烟草证号查询响应报文]：' . print_r($query_res, true));
    $res_data = json_decode($query_res,true);
    if($res_data['ret_code'] == '0000'){
        return true;
    }
    return false;
}

/**
 * 格式化参数格式化成url参数
 */
function toUrlParams($data)
{
    $buff = "";
    foreach ($data as $k => $v)
    {
        if($k != "sign" && $v != "" && !is_array($v)){
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}

/**
 * 生成签名
 * @param WxPayConfigInterface $config  配置对象
 * @param bool $needSignType  是否需要补signtype
 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
 */
function makeSign($data,$key)
{
    //签名步骤一：按字典序排序参数
    ksort($data);
    $string = toUrlParams($data);
    //签名步骤二：在string后加入KEY
    #$string = $string . "&key=renlianzhifu45879576894859307652";
    $string = $string .$key;
    //签名步骤三：MD5加密或者HMAC-SHA256
    #echo $string;exit;
    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
}