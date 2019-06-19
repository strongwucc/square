<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class O2oTitleType extends Model
{
    protected $table = 'o2o_title_type';
    public $timestamps = false;

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
