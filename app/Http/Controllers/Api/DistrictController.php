<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function notify()
    {
        echo 'success';
    }

    public function info()
    {
        $name = config('trading.name');
        $picture = config('trading.picture');

        $info = ['name' => $name, 'picture' => $picture];

        return $this->response->array($info);
    }
}
