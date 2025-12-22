<!-- // config/wechat.php
return [
    // 公众号配置
    'official_account' => [
        'default' => [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', ''),
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', ''),
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', ''),
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', ''),
            
            // 网页授权回调地址
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', ''),
            ],
        ],
    ],
    
    // 其他配置...
]; -->