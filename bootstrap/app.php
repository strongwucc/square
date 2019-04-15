<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function ($app) {

    $map_data = [];

    $mysqli = new mysqli(env('DB_MAP_HOST'), env('DB_MAP_USERNAME'), env('DB_MAP_PASSWORD'), env('DB_MAP_DATABASE'));

    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') '
                . $mysqli->connect_error);
    }

    if (!$mysqli->set_charset("utf8")) {
        die("Error loading character set utf8");
    }

//    $fullUrl = url()->full();
    $fullUrl = 'http://district.test/api/';
    $urlInfo = parse_url($fullUrl);
    $host = explode('.', $urlInfo['host']);
    $districtName = $host[0];

    // 查询数据库连接信息
    if ($result = mysqli_query($mysqli, "SELECT trading_id, trading_ku_address,user_name,user_password FROM sdb_basic_trading_ku WHERE trading_ku_name = '" . $districtName . "' LIMIT 1")) {
        $map_data = $result->fetch_array(MYSQLI_ASSOC);

        /* free result set */
        mysqli_free_result($result);
    } else {
        die('Error selecting...');
    }

    // 查询支付配置信息
    if ($result = mysqli_query($mysqli, "SELECT merchantId,datakey,back_url FROM mch_etongpay_key WHERE business_circle = '" . $districtName . "' LIMIT 1")) {
        $pay_data = $result->fetch_array(MYSQLI_ASSOC);

        /* free result set */
        mysqli_free_result($result);
    } else {
        die('Error selecting...');
    }

    $payInfo = [
        'etonepay.mch_id' => '',
        'etonepay.mch_key' => '',
        'etonepay.back_url' => ''
    ];

    if (!empty($pay_data['merchantId'])) {
        $payInfo['etonepay.mch_id'] = $pay_data['merchantId'];
    }
    if (!empty($pay_data['datakey'])) {
        $payInfo['etonepay.mch_key'] = $pay_data['datakey'];
    }
    if (!empty($pay_data['back_url'])) {
        $payInfo['etonepay.back_url'] = $pay_data['back_url'];
    }

    config($payInfo);

    // 商圈信息
    if ($result = mysqli_query($mysqli, "SELECT trading_name,trading_picture FROM sdb_basic_trading WHERE id = '" . $map_data['trading_id'] . "' LIMIT 1")) {
        $trading_data = $result->fetch_array(MYSQLI_ASSOC);

        /* free result set */
        mysqli_free_result($result);
    } else {
        die('Error selecting...');
    }

    $tradingInfo = [
        'trading.name' => '',
        'trading.picture' => ''
    ];

    if (!empty($trading_data['trading_name'])) {
        $tradingInfo['trading.name'] = $trading_data['trading_name'];
    }
    if (!empty($trading_data['trading_picture'])) {
        $tradingInfo['trading.picture'] = $trading_data['trading_picture'];
    }

    config($tradingInfo);

    $mysqli->close();

    if (empty($map_data['trading_ku_address']) || empty($map_data['user_name'] || empty($map_data['user_password']))) {
        die('Get database error...');
    }

    config([
        'database.connections.mysql.host' => $map_data['trading_ku_address'],
        'database.connections.mysql.database' => $districtName,
        'database.connections.mysql.username' => $map_data['user_name'],
        'database.connections.mysql.password' => $map_data['user_password'],
    ]);
});

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
