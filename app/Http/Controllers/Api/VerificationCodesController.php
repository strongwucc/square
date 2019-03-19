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

        try {
            $result = send_msg($mobile, '短信验证码：'.$code.',请不要把验证码泄露给其他人，如非本人操作，请勿理会！4分钟内有效。');
            Log::info('短信验证码发送返回报文]：'.json_encode($result));
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return $this->errorResponse(403, $message ?: '短信发送异常', 1003);
        }

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
