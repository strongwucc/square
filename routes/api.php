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
    'namespace' => 'App\Http\Controllers\Api'
], function($api) {
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
});
