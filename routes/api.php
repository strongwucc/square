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
    'middleware' => ['serializer:array', 'bindings', 'change-locale', 'api-log']
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');

        // 热门活动
        $api->post('activities', 'ActivitiesController@index')
            ->name('api.activities.index');

        // 首页 banner
        $api->post('banners', 'BannersController@index')
            ->name('api.banners.index');

        // 商户类型
        $api->post('merchant_types', 'MerchantsController@merchantTypes')
            ->name('api.merchants.types');

        // 热门商户
        $api->post('merchant_hots', 'MerchantsController@merchantHots')
            ->name('api.merchants.hots');

        // 热门搜索关键字
        $api->post('search_keywords', 'SearchKeywordsController@index')
            ->name('api.search_keywords.index');

        // 商户列表
        $api->post('merchants', 'MerchantsController@merchants')
            ->name('api.merchants.merchants');

        // 商户详情
        $api->post('merchants/{o2oMerchant}', 'MerchantsController@show')
            ->name('api.merchants.show');

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->post('user', 'UsersController@me')
                ->name('api.user.show');
            // 刷新token
            $api->post('authorizations/update', 'AuthorizationsController@update')
                ->name('api.authorizations.update');
            // 删除token
            $api->post('authorizations/destroy', 'AuthorizationsController@destroy')
                ->name('api.authorizations.destroy');
        });
    });

});
