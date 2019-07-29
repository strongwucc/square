<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings', 'change-locale', 'api-log', 'cros']
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        // 商圈信息
        $api->post('district/info', 'DistrictController@info')
            ->name('api.district.info');

        // 第三方登录
        $api->post('socials/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');

        // 第三方登录(etone)
        $api->post('etone/authorizations', 'AuthorizationsController@etoneStore')
            ->name('api.etone.authorizations.store');

        // 根据 openid 登录
        $api->post('openid/authorizations', 'AuthorizationsController@autoLoginByOpenId')
            ->name('api.openid.authorizations.store');

        // 热门活动
        $api->post('activities', 'ActivitiesController@index')
            ->name('api.activities.index');

        // 首页 banner
        $api->post('banners', 'BannersController@index')
            ->name('api.banners.index');

        // 商户类型
        $api->post('merchants/types', 'MerchantsController@merchantTypes')
            ->name('api.merchants.types');

        // 热门商户
        $api->post('merchants/hots', 'MerchantsController@merchantHots')
            ->name('api.merchants.hots');

        // 热门搜索关键字
        $api->post('search_keywords', 'SearchKeywordsController@index')
            ->name('api.search_keywords.index');

        // 商户列表
        $api->post('merchants', 'MerchantsController@merchants')
            ->name('api.merchants.merchants');

        // 商户详情
        $api->post('merchant', 'MerchantsController@show')
            ->name('api.merchants.show');

        // 优惠券列表
        $api->post('coupons', 'CouponsController@index')
            ->name('api.coupons.index');

        // 优惠券详情
        $api->post('coupon/detail', 'CouponsController@detail')
            ->name('api.coupons.detail');

        // 根据 openid 查询所有优惠券
        $api->post('coupons/openid', 'CouponsController@couponsByOpenid')
            ->name('api.coupons.openid');

        // 根据 openid 查询优惠券详情
        $api->post('coupons/openid_detail', 'CouponsController@couponByOpenid')
            ->name('api.coupons.openid_detail');

        // 根据 openid 核销优惠券
        $api->post('coupon/write_off', 'CouponsController@writeOff')
            ->name('api.coupons.write_off');

        // 支付通知
        $api->post('pay/notify', 'DistrictController@notify')
            ->name('api.pay.notify');

        // 刷新token
        $api->post('authorizations/update', 'AuthorizationsController@update')
            ->name('api.authorizations.update');

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {

            // 当前登录用户信息
            $api->post('user', 'UsersController@me')
                ->name('api.user.show');

            // 短信验证码
            $api->post('verification_codes', 'VerificationCodesController@store')
                ->name('api.verification_codes.store');

            // 手机绑定
            $api->post('user/bind', 'UsersController@bind')
                ->name('api.users.bind');

            // 删除token
            $api->post('authorizations/destroy', 'AuthorizationsController@destroy')
                ->name('api.authorizations.destroy');

            // 用户优惠券列表
            $api->post('user/coupons', 'UsersController@coupons')
                ->name('api.user.coupons');

            // 收藏商户
            $api->post('fav', 'UsersController@fav')
                ->name('api.user.fav');

            // 收藏商户列表
            $api->post('favs', 'UsersController@favs')
                ->name('api.user.favs');

            // 积分列表
            $api->post('points', 'UsersController@points')
                ->name('api.user.points');

            // 领取优惠券TODO
            $api->post('coupon/receive', 'CouponsController@receive')
                ->name('api.coupons.receive');

            // 我的优惠券详情
            $api->post('coupon/show', 'CouponsController@show')
                ->name('api.coupons.show');

            // 修改用户信息
            $api->post('user/edit', 'UsersController@edit')
                ->name('api.user.edit');

            // 订单列表
            $api->post('user/orders', 'UsersController@orders')
                ->name('api.user.orders');


        });
    });

});
