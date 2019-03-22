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
    $res_json = @preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($post_data));
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
    return date('YmdHis', time()) . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT) ;
}

function timestamp_cmp($a, $b)
{
    return $b->tran_time - $a->tran_time;
}
