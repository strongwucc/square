<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Facades\Log;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request)
    {
        $mobile = $request->mobile;

        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        //*//
        try {
            $content = '【e通宝】，短信验证码：' . $code . '，请不要把验证码泄露给其他人，如非本人操作，请勿理会！4分钟内有效。';
            $result = hk_sms_send($mobile, $content);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse(403, $message ?: '短信发送异常', 1003);
        }
        //*/
        /*//
        $sms = app('easysms');
        try {
            Log::channel('sms')->info('短信验证码发送手机号]：' . $mobile);
            Log::channel('sms')->info('短信验证码发送内容]：' . $code);
            $sms->send($mobile, [
                'content'  => $code,
                'template'=>'SMS_178520287',
                'data'=>[
                    'code'=>$code
                ]
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('aliyun')->getMessage();
            Log::channel('sms')->info('短信验证码发送异常：' . $message);
        }
        //*/

        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes(4);
        // 缓存验证码 4分钟过期。
        \Cache::put($key, ['phone' => $mobile, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
