<?php

namespace App\Http\Controllers\Api;

use App\Models\O2oSearchKeyword;
use Illuminate\Http\Request;
use App\Transformers\SearchKeywordTransformer;

class SearchKeywordsController extends Controller
{
    public function index(O2oSearchKeyword $searchKeyword)
    {
        $query = $searchKeyword->query();
        $query->where('status', 0);
        $query->recentReplied();
        $keywords = $query->get();

        return $this->response->collection($keywords, new SearchKeywordTransformer());
    }
}
