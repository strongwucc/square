<?php

namespace App\Http\Controllers\Api;

use App\Models\O2oActivity;
use Illuminate\Http\Request;
use App\Transformers\ActivityTransformer;

class DistrictController extends Controller
{
    public function test()
    {
        return $this->response->collection(O2oActivity::all(), new ActivityTransformer());
    }
}
