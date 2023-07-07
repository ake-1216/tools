<?php

use GuzzleHttp\Client;
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
        if (strpos($content, 'http') === false){
            $storage = Storage::disk($disk);
            return $storage->url($content);
        }
        return $content;
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
            'data' => is_array($data) ? $data : json_decode(json_encode($data), 1),
            'time' => time(),
        ], $code);
    }
}

if (!function_exists('txVideoUrl2MiniUrl')){
    /**
     * @description:腾讯视频地址生成可小程序播放地址
     * @param string $url
     * @return string
     * @Author:AKE
     * @Date:2022/9/14 10:32
     */
    function txVideoUrl2MiniUrl(string $url)
    {
        $vids = strstr($url, 'page/');//字符串查询到page/o0560pmnr2z.html
        $vids_arr = explode('/', $vids);//转为数组
        $vid = strstr($vids_arr[1], '.html', true);//获取o0560pmnr2z

        $api_url = 'http://vv.video.qq.com/getinfo?vids=' . $vid . '&platform=101001&charge=0&otype=json';//接口地址
        $res_json = (new Client())->get($api_url)->getBody()->getContents();
        $str = str_replace('QZOutputJson=', '', $res_json);
        $str1 = str_replace('};', '}', $str);
        $res = json_decode($str1, true);

        if ($res['code'] == "0.0") {
            $url = $res['vl']['vi'][0]['ul']['ui'][0]['url'];
            $fn = $res['vl']['vi'][0]['fn'];
            $fvkey = $res['vl']['vi'][0]['fvkey'];
            return $url . $fn . '?vkey=' . $fvkey;
        }
        return $url;
    }
}


if (!function_exists('getAdminSetting')){
    /**
     * @description:setting缓存
     * @param $key admin_setting 表 slug
     * @param $sub_key json数组的key 可以用 . 链接
     * @param $default 获取不到的默认值
     * @return mixed
     * @Author:AKE
     * @Date:2023/3/23 14:00
     */
    function getAdminSetting($key, $sub_key = null, $default = null)
    {
        $setting = (new \Ake\Tools\Services\AdminSetting())->get();
        $res = array_get($setting, $key, $default);
        if (!is_null($sub_key))
            return  is_array($res) ? Arr::get($res, $sub_key, $default) : Arr::get(json_decode($res, true), $sub_key, $default);
        try{
            return json_decode($res, true);
        }catch (\Exception $e){
            return $res;
        }
    }
}