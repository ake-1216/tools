<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

if (!function_exists('disk')) {
    /**
     * @description:
     * @param $content
     * @param string $disk
     * @return mixed
     * @Author:AKE
     * @Date:2021/8/11 14:42
     */
    function disk($content, $disk = '')
    {
        if (!$content) return '';
        $disk = $disk ?: config('admin.upload.disk');
        $storage = Storage::disk($disk);
        return $storage->url($content);
    }
}

if (!function_exists('diffForHumans')){
    /**
     * @description:获取当前时间，距离现在的时间
     * @param $time
     * @return string
     * @Author:AKE
     * @Date:2022/7/6 13:22
     */
    function diffForHumans($time)
    {
        $dt = Carbon::create($time);
        return $dt->diffForHumans();
    }
}