<?php
/**
 * @file service 基类
 */

namespace Ake\Tools\Laravel\Base;

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

    #详情
    public function detail(int $id)
    {
        return $this->model()->find($id)->toArray();
    }

    #seo 获取
    public function seo(int $id)
    {
        return $this->model()->select(['seo_title', 'seo_keyword', 'seo_description'])->find($id)->toArray();
    }
}