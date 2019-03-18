<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function test()
    {
        return $this->response->array(['test_message' => 'store verification code']);
    }
}
