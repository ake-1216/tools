<?php

return [
    #微信小程序配置
    'mini' => [
        'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
        'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
        'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
        'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
    ],
    #第三方web pc端网页授权登录
    'pc' => [
        'app_id'   => env('WECHAT_PC_APP_ID', ''),
        'secret'   => env('WECHAT_PC_APP_SECRET', ''),
        'token'    => env('WECHAT_PC_TOKEN', ''),
        'aes_key'  => env('WECHAT_PC_AES_KEY', ''),
    ],
    #手机端微信浏览器公众号登录
    'mb' => [
        'app_id' => env('WECHAT_MB_APP_ID', ''),
        'secret' => env('WECHAT_MB_APP_SECRET', ''),
        'response_type' => 'array',
    ],
    #手机回调
    'mb-callback' => '',
];
