<?php

namespace App\Http\Controllers\Api;

use App\Models\O2oBanner;
use Illuminate\Http\Request;
use App\Transformers\BannerTransformer;

class BannersController extends Controller
{
    public function index(O2oBanner $banner)
    {
        $query = $banner->query();
        $query->where('is_del', 0);
        $banners = $query->get();
        return $this->response->collection($banners, new BannerTransformer());
    }
}
