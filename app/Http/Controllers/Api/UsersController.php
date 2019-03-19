<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function bind(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            // return $this->response->error('验证码已失效', 422);
            return $this->errorResponse(422, '验证码已失效', 1003);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            // return $this->response->errorUnauthorized('验证码错误');
            return $this->errorResponse(401, '验证码错误', 1003);

        }

        $user = $this->user();

        $user->update(['mobile'=>$request->mobile]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->created();
    }
}
