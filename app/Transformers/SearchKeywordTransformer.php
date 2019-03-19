<?php

namespace App\Transformers;

use App\Models\O2oSearchKeyword;
use League\Fractal\TransformerAbstract;

class SearchKeywordTransformer extends TransformerAbstract
{
    public function transform(O2oSearchKeyword $searchKeyword)
    {
        return [
            'id' => $searchKeyword->id,
            'keyword' => $searchKeyword->keyword,
        ];
    }
}
