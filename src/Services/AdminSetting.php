<?php
/**
 * @file admin_setting service
 */
namespace Ake\Tools\Services;

use Illuminate\Support\Facades\Cache;

class  AdminSetting
{
    protected $cache_key = 'admin_setting';

    #获取内容
    public function get()
    {
        $setting = json_decode(Cache::get($this->cache_key), true);
        if (!$setting)  $setting = $this->saveCache();
        return $setting;
    }

    #保存缓存
    public function saveCache()
    {
        $data = admin_setting()->toArray();

        $expire = config('cache.expire_at', 60 * 60 * 24);

        Cache::put($this->cache_key, json_encode($data), $expire);

        return $data;
    }
}