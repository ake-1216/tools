<?php

/**
 * @file Model 基类
 */

namespace Ake\Tools\Laravel\Base;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Base;
use Illuminate\Support\Facades\DB;

class Model extends Base
{
    use HasFactory,HasDateTimeFormatter;

    /**
     * @description:find_in_set 方法
     * @param $query
     * @param $field
     * @param $value
     * @Author:AKE
     * @Date:2022/1/17 15:50
     */
    public function scopeFindInSet($query, $field,  $value)
    {
        $query->whereRaw("FIND_IN_SET(?,$field)", $value);
    }

    /**
     * @description:
     * @param $query
     * @Author:AKE
     * @Date:2022/6/14 10:29
     */
    public function scopePublish($query)
    {
        $query->where('is_publish', 1);
    }

    /**
     * @description:汉字排序
     * @param $query
     * @param string $field 需要排序字段名
     * @param string $order 排序规则
     * @Author:AKE
     * @Date:2022/2/21 14:54
     */
    public function scopeGbkOrder($query, string $field, string $order = 'asc')
    {
        $query->orderBy(DB::raw("convert(`{$field}` using gbk)"), $order);
    }
}