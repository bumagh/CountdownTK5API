<?php

return [
    // 公众号（服务号）配置
    'official_account' => [
        'app_id' => env('WECHAT_OFFICIAL_APPID', ''),
        'secret' => env('WECHAT_OFFICIAL_SECRET', ''),

        // 模板消息默认模板ID（可在接口调用时覆盖）
        'template_id' => env('WECHAT_TEMPLATE_ID', ''),

        // 消息跳转链接（可选，接口调用时也可覆盖）
        'url' => env('WECHAT_TEMPLATE_URL', ''),

        // 小程序跳转（可选）
        'miniprogram' => [
            'appid' => env('WECHAT_MINIPROGRAM_APPID', ''),
            'pagepath' => env('WECHAT_MINIPROGRAM_PAGEPATH', ''),
        ],
    ],
];
