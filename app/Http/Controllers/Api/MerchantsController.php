<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\O2oMerchantHot;
use App\Models\O2oMerchantType;
use App\Models\O2oMerchant;

use App\Transformers\MerchantHotTransformer;
use App\Transformers\MerchantTypeTransformer;
use App\Transformers\MerchantTransformer;

class MerchantsController extends Controller
{
    public function merchantHots(Request $request, O2oMerchantHot $merchantHot)
    {
        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;

        $query = $merchantHot->query();
        $query->recentReplied();
        $hots = $query->paginate($pageLimit);

        return $this->response->paginator($hots, new MerchantHotTransformer());
    }

    public function merchantTypes(Request $request, O2oMerchantType $merchantType)
    {
        $pcode = $request->pcode ? intval($request->pcode) : 0;

        $query = $merchantType->query();
        $query->where('pcode', $pcode);
        $types = $query->get();

        return $this->response->collection($types, new MerchantTypeTransformer());
    }

    public function merchants(Request $request, O2oMerchant $merchant)
    {
        $pageLimit = $request->page_limit ? $request->page_limit : $this->pageLimit;

        $query = $merchant->query();

        if ($typeCode = $request->type_code) {
            $query->where('type_code', $typeCode);
        }

        if ($searchKey = $request->search_key) {
            $query->where('mer_name', 'like', '%'.$searchKey.'%');
        }

        if ($perCostMin = $request->per_cost_min) {
            $query->where('per_cost', '>=', $perCostMin);
        }

        if ($perCostMax = $request->per_cost_max) {
            $query->where('per_cost', '<=', $perCostMax);
        }

        $query->recentReplied();
        $merchants = $query->paginate($pageLimit);

        return $this->response->paginator($merchants, new MerchantTransformer());
    }

    public function show(O2oMerchant $o2oMerchant)
    {
        return $this->response->item($o2oMerchant, new MerchantTransformer());
    }
}
