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

});
