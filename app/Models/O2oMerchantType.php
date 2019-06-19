<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class O2oMerchantType extends Model
{
    use ModelTree, AdminBuilder;
    protected $primaryKey = 'type_code';
    protected $table = 'o2o_merchant_type';
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('pcode');
        $this->setOrderColumn('sort_rank');
        $this->setTitleColumn('type_name');
    }

    public function scopeWithOrder($query, $order)
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }
    }

    public function scopeRecentReplied($query)
    {
        return $query->orderBy('sort_rank', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function typePath($typeCode)
    {
        $query = $this->query();
        $query->select('type_code');
        $query->where('is_del', 0);
        $query->where('pcode', $typeCode);
        $codes = $query->get();

        $typeCodes = [$typeCode];

        foreach ($codes as $code) {
            $typeCodes[] = $code->type_code;
        }

        return $typeCodes;
    }

    public function getTypeCode()
    {
        $now_time = time();
        do {
            $type_code = get_type_code($now_time);
            $row = $this->where('type_code', $type_code)->count();

        } while ($row);

        return $type_code;
    }
}
