<?php

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