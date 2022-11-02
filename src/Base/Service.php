<?php
/**
 * @file service 基类
 */

namespace Ake\Tools\Base;

use Illuminate\Database\Eloquent\Builder;

class Service
{
    #模型
    protected $modelClass;

    /**
     * @description:模型类对象
     * @return Builder
     * @Author:AKE
     * @Date:2022/7/7 14:34
     */
    public function model()
    {
        return (new $this->modelClass)->newQuery();
    }

    #根据条件获取记录
    public function first(array $w)
    {
        return $this->model()->where($w)->first();
    }

    #详情
    public function find(int $id)
    {
        return $this->model()->find($id);
    }

    #seo 获取
    public function seo(int $id)
    {
        return $this->model()->select(['seo_title', 'seo_keyword', 'seo_description'])->find($id);
    }

    #维护时间
    public function times(string $time = '')
    {
        $date = date('Y-m-d H:i:s');
        if ($time) return [ $time => $date ];
        return [
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}