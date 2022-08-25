<?php

if(!function_exists('enterLineFeed')){
    /**
     * @description:多行文本框,回车转换行
     * @param string $string 需转换的文本内容(字符串)
     * @return array|mixed|string|string[] 转换后的文本内容
     * @Author:AKE
     * @Date:2021/11/8 15:53
     */
    function enterLineFeed(string $string = '')
    {
        return str_replace("\r\n","<br>",$string);
    }
}

if (!function_exists('infinite')) {
    /**
     * @description:无限极分类数据处理
     * @param $nodes 需要处理的数组
     * @param int $tier 循环层数
     * @param int $parentId 父级id
     * @param string $prefix 前缀
     * @param string $title title名
     * @param string $idname id名
     * @return array 数组
     * @Author:AKE
     * @Date:2021/6/15 15:12
     */
    function infinite($nodes, $tier = 999, $parentId = 0, $prefix = '', $title = 'name', $idname = 'id')
    {
        $tier--;
        if ($tier == 0) return 0;
        $options = [];
        $d = '├─';
        $space = '&nbsp;';
        $prefix = $prefix ?: $d . $space;
        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $currentPrefix = $prefix;
                $node[$title] = $currentPrefix . $space . $node[$title];
                $childrenPrefix = str_replace($d, str_repeat($space, 6), $prefix) . $d . str_replace([$d, $space], '', $prefix);
                $children = infinite($nodes, $tier, $node['id'], $childrenPrefix, $title, $idname);
                $options[$node[$idname]] = $node[$title];
                if ($children) $options += $children;
            }
        }
        return $options;
    }
}

if (!function_exists('arrayTree')) {
    /**
     * @see 完成无限级分类
     * @param $array
     * @param string $key
     * @param int $id
     * @return array
     */
    function arrayTree($array, $key='parent_id', $id = 0)
    {
        $newArray = $filterArray = array_filter($array, function ($a) use($key, $id) {
            return $a[$key] == $id;
        });
        $orders = array_column($newArray, 'order');
        $ids = array_column($newArray, 'id');
        array_multisort($orders, SORT_ASC, $ids, SORT_ASC, $newArray, $filterArray);

        foreach ($filterArray as $k => $filter) {
            $newArray[$k]['child'] = arrayTree($array, $key, $filter['id']);
        }

        return $newArray;
    }
}

if (!function_exists('objToArr')){
    /**
     * @description:对象转数组
     * @param $obj
     * @return mixed
     * @Author:AKE
     * @Date:2022/1/12 9:48
     */
    function objToArr($obj)
    {
        return json_decode(json_encode($obj), true);
    }
}

if (!function_exists('formatTime')){
    /**
     * @description: 时间格式化
     * @param $time
     * @param string $format
     * @return false|string
     * @Author:AKE
     * @Date:2022/7/6 9:41
     */
    function formatTime($time, $format = 'Y-m-d H:i:s')
    {
        return date($format, strtotime($time));
    }
}

if (function_exists('editorToText')) {
    /**
     * @description: 富文本内容转纯文字
     * @param string $editor 富文本内容
     * @param int $num 截取字数,默认截取全部
     * @return string 返回处理之后的文本
     * @Author: AKE
     * @Date: 2021-05-25 15:19:00
     */
    function editorToText(string $editor, $num = 2147483647)
    {
        $content_01 = $editor; //富文本内容
        $content_02 = htmlspecialchars_decode($content_01); //把一些预定义的 HTML 实体转换为字符
        $content_03 = str_replace("&nbsp;", "", $content_02); //将空格替换成空
        $contents = strip_tags($content_03); //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        $con = mb_substr($contents, 0, $num, "utf-8"); //返回字符串中的前100字符串长度的字符
        return $con;
    }
}

if (!function_exists('scandirFolder')) {
    /**
     * @description: 根据目录名扫描获取此目录下所有文件夹及文件
     * @param string 文件目录
     * @return array 包含所有文件及文件夹的数组
     * @Author: AKE
     * @Date: 2021-05-24 10:03:56
     */
    function scandirFolder($path)
    {
        $list     = [];
        $temp_list = scandir($path, 0);
        foreach ($temp_list as $file) {
            //排除根目录
            if ($file != ".." && $file != ".") {
                if (is_dir($path . "/" . $file)) {
                    //子文件夹，进行递归
                    $list[$file] = scandirFolder($path . "/" . $file);
                } else {
                    //根目录下的文件
                    $list[] = $file;
                }
            }
        }
        return $list;
    }
}

if(!function_exists('isMobile')){
    /**
     * @description:判断是否是手机访问
     * @return bool 是返回 true 否返回false
     * @Author:AKE
     * @Date:2022/8/19 9:59
     */
    function isMobile()
    {
        #获取请求头的浏览器类型
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        #判断是否是 windows
        $is_pc = (strpos($agent, 'windows nt')) ? true : false;
        #判断是否是 mac
        $is_mac = (strpos($agent, 'mac os')) ? true : false;
        #判断是否是 iphone
        $is_iphone = (strpos($agent, 'iphone')) ? true : false;
        #判断是否是 android
        $is_android = (strpos($agent, 'android')) ? true : false;
        #判断是否是 ipad
        $is_ipad = (strpos($agent, 'ipad')) ? true : false;
        #如果是 window 或者 mac 则为pc 返回 false
        if ($is_pc || $is_mac) return false;
        #如果是 iphone ，android ， ipad 则为手机 返回 true
        if ($is_iphone || $is_android || $is_ipad)  return true;
    }
}