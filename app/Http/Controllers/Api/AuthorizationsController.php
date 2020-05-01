<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use Illuminate\Support\Facades\Log;

class AuthorizationsController extends Controller
{

    public function autoLoginByOpenId(Request $request)
    {
        $open_id = $request->openId;

        if (!$open_id) {
            return $this->response->errorBadRequest();
        }

        $user = User::where('openid', $open_id)->first();
        if (!$user) {
            $platform_member_id = 0;

            do {

                $platform_member_id = get_member_id();
                $row = User::where('platform_member_id', $platform_member_id)->count();

            } while ($row);

            $user = User::create([
                'nickname' => '',
                'headimgurl' => '',
                'openid' => $open_id,
                'unionid' => '',
                'regtime' => time(),
                'platform_member_id' => $platform_member_id,
                'source' => 'api'
            ]);
        }

        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function etoneStore(Request $request)
    {

        $code = $request->code;

        if (!$code) {
            return $this->response->errorBadRequest();
        }

        // 获取 access_token
        $token_url = env('WXGW_BASE_URL') . 'accessToken';
        $token_post_data = [
            'etone_id' => env('ETONE_ID'),
            'code' => $code
        ];

        Log::channel('weixin')->info('获取 access_token 地址：' . $token_url);
        Log::channel('weixin')->info('获取 access_token 参数：' . print_r($token_post_data, true));

        $token_res = post_json($token_url, $token_post_data);

        Log::channel('weixin')->info('获取 access_token 结果：' . $token_res);

        $token_data = json_decode($token_res, true);

        if (isset($token_data['return_code']) && $token_data['return_code'] == '0000') {
            $access_token = $token_data['data']['access_token'];
            $token_data['expires_in'] = time() + $token_data['data']['expires_in'];
            $openid = $token_data['data']['openid'];

            $userinfo_url = env('WXGW_BASE_URL') . 'userInfo';
            $userinfo_post_data = [
                'etone_id' => env('ETONE_ID'),
                'access_token' => $access_token,
                'openid' => $openid
            ];

            Log::channel('weixin')->info('获取 userinfo 地址：' . $userinfo_url);
            Log::channel('weixin')->info('获取 userinfo 参数：' . print_r($userinfo_post_data, true));

            $userinfo_res = post_json($userinfo_url, $userinfo_post_data);

            Log::channel('weixin')->info('获取 userinfo 结果：' . $userinfo_res);
            $userinfo_data = json_decode($userinfo_res, true);

            if (isset($userinfo_data['return_code']) && $userinfo_data['return_code'] == '0000') {
                $user = User::where('openid', $openid)->first();
                if (!$user) {

                    $platform_member_id = 0;

                    do {

                        $platform_member_id = get_member_id();
                        $row = User::where('platform_member_id', $platform_member_id)->count();

                    } while ($row);

                    $user = User::create([
                        'nickname' => emoji_encode($userinfo_data['data']['nickname']),
                        'headimgurl' => $userinfo_data['data']['headimgurl'],
                        'openid' => $openid,
                        'unionid' => isset($userinfo_data['data']['unionid']) ? $userinfo_data['data']['unionid'] : '',
                        'regtime' => time(),
                        'platform_member_id' => $platform_member_id,
                        'source' => 'weixin'
                    ]);
                }

                $token = Auth::guard('api')->fromUser($user);
                return $this->respondWithToken($token)->setStatusCode(201);

            } else {
                return $this->errorResponse(422, '获取 userinfo 失败', 1003);
            }


        } else {
            return $this->errorResponse(422, '获取 access_token 失败', 1002);
        }

    }

    public function socialStore(SocialAuthorizationRequest $request)
    {

        $type = $request->type;

        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }

        $driver = \Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;

                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            // return $this->response->errorUnauthorized('参数错误，未获取用户信息');
            Log::channel('weixin')->info('获取用户信息失败：' . $e->getMessage());
            return $this->errorResponse(403, '系统开小差啦，请重试', 1003);
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('unionid', $unionid)->first();
                } else {
                    $user = User::where('openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {

                    $platform_member_id = 0;

                    do {

                        $platform_member_id = get_member_id();
                        $row = User::where('platform_member_id', $platform_member_id)->count();

                    } while ($row);

                    $user = User::create([
                        'nickname' => $oauthUser->getNickname(),
                        'headimgurl' => $oauthUser->getAvatar(),
                        'openid' => $oauthUser->getId(),
                        'unionid' => $unionid,
                        'regtime' => time(),
                        'platform_member_id' => $platform_member_id,
                        'source' => 'weixin'
                    ]);
                }

                break;
        }

        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
