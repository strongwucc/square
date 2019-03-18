<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\O2oMerchantHot;
use App\Models\O2oMerchantType;

use App\Transformers\MerchantHotTransformer;
use App\Transformers\MerchantTypeTransformer;

class MerchantsController extends Controller
{
    public function merchantHots(Request $request, O2oMerchantHot $merchantHot)
    {
        $pageLimit = $request->pageLimit ? $request->pageLimit : $this->pageLimit;

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
}
