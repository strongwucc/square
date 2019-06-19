<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->get('orders', 'O2oOrderController@index');

    $router->get('merchant_types', 'O2oMerchantTypeController@index');
    $router->get('merchant_types/create', 'O2oMerchantTypeController@create');
    $router->post('merchant_types', 'O2oMerchantTypeController@store');
    $router->get('merchant_types/{id}/edit', 'O2oMerchantTypeController@edit');
    $router->get('api/merchant_types', 'O2oMerchantTypeController@apiTypes');
    $router->put('merchant_types/{id}', 'O2oMerchantTypeController@update');
    $router->delete('merchant_types/{id}', 'O2oMerchantTypeController@destroy');

    $router->get('pic_types', 'O2oPicTypeController@index');
    $router->get('pic_types/create', 'O2oPicTypeController@create');
    $router->post('pic_types', 'O2oPicTypeController@store');
    $router->get('pic_types/{id}/edit', 'O2oPicTypeController@edit');
    $router->put('pic_types/{id}', 'O2oPicTypeController@update');
    $router->delete('pic_types/{id}', 'O2oPicTypeController@destroy');
    $router->get('pic_types/{id}', 'O2oPicTypeController@show');

    $router->get('title_types', 'O2oTitleTypeController@index');
    $router->get('title_types/create', 'O2oTitleTypeController@create');
    $router->post('title_types', 'O2oTitleTypeController@store');
    $router->get('title_types/{id}/edit', 'O2oTitleTypeController@edit');
    $router->put('title_types/{id}', 'O2oTitleTypeController@update');
    $router->delete('title_types/{id}', 'O2oTitleTypeController@destroy');
    $router->get('title_types/{id}', 'O2oTitleTypeController@show');

    $router->get('search_keywords', 'O2oSearchKeywordController@index');
    $router->get('search_keywords/create', 'O2oSearchKeywordController@create');
    $router->post('search_keywords', 'O2oSearchKeywordController@store');
    $router->get('search_keywords/{id}/edit', 'O2oSearchKeywordController@edit');
    $router->put('search_keywords/{id}', 'O2oSearchKeywordController@update');
    $router->delete('search_keywords/{id}', 'O2oSearchKeywordController@destroy');
    $router->get('search_keywords/{id}', 'O2oSearchKeywordController@show');

    $router->get('merchant_hots', 'O2oMerchantHotController@index');
    $router->get('merchant_hots/create', 'O2oMerchantHotController@create');
    $router->post('merchant_hots', 'O2oMerchantHotController@store');
    $router->get('merchant_hots/{id}/edit', 'O2oMerchantHotController@edit');
    $router->put('merchant_hots/{id}', 'O2oMerchantHotController@update');
    $router->delete('merchant_hots/{id}', 'O2oMerchantHotController@destroy');
    $router->get('merchant_hots/{id}', 'O2oMerchantHotController@show');

});
