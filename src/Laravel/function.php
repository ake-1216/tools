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

if (!function_exists("error")){
    /**
     * @description:错误返回
     * @param string $msg
     * @param int $error_code
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     * @Author:AKE
     * @Date:2022/4/8 10:42
     */
    function error(string $msg = "", int $error_code = 10001, int $code = 200)
    {
        return response()->json([
            "error_code" => $error_code,
            "msg" => $msg,
            'time' => time(),
        ],$code);
    }
}


if (!function_exists("success")){
    /**
     * @description:成功返回
     * @param $data
     * @param string $msg
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     * @Author:AKE
     * @Date:2022/3/14 10:49
     */
    function success($data = [], string $msg = '查询成功',  int $code = 201)
    {
        return response()->json([
            "error_code" => 0,
            'msg' => $msg,
            'data' => $data,
            'time' => time(),
        ], $code);
    }
}