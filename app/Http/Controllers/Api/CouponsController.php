<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\O2oCoupon;

use App\Transformers\CouponTransformer;

class CouponsController extends Controller
{
    public function index(Request $request, O2oCoupon $coupon)
    {
        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;

        $query = $coupon->query();

        if ($merId = $request->mer_id) {
            $query->where('mer_id', $merId);
        }

        if ($cardType = $request->card_type) {
            $query->where('card_type', $cardType);
        }

        $query->where('is_del', 0);
        $query->where('coupon_status', 0);

        $query->recentReplied();
        $coupons = $query->paginate($pageLimit);

        return $this->response->paginator($coupons, new CouponTransformer());
    }
}
