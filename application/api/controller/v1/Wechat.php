<?php
// app/api/controller/WechatController.php

namespace app\api\controller\v1;

use think\Request;
use think\Response;
use Firebase\JWT\JWT;
use think\facade\Log;
use think\facade\Cache;
use think\facade\Config;
use app\common\model\UserModel;
use app\common\service\JwtService;

class Wechat
{
    protected $appId = "wxc164b903f978d83d";
    protected $appSecret = "b296cbe4bb23e7714b38ab6f23ac7b8e";
    /**
     * 微信公众号网页授权登录
     * @param Request $request
     * @return Response
     */
    public function loginByWeixin(Request $request)
    {
        try {
            // 1. 获取参数
            $code = $request->param('code', '');
            if (empty($code)) {
                return json([
                    'code' => 400,
                    'message' => '缺少授权码',
                    'data' => null
                ]);
            }

            // 2. 使用code换取access_token和openid
            $wechatData = $this->getWechatAccessToken($code);
            if (!$wechatData) {
                return json([
                    'code' => 400,
                    'message' => '微信授权失败',
                    'data' => null
                ]);
            }

            $openid = $wechatData['openid'];
            $accessToken = $wechatData['access_token'];

            // 3. 获取微信用户信息
            $userInfo = $this->getWechatUserInfo($accessToken, $openid);
            if (!$userInfo) {
                return json([
                    'code' => 400,
                    'message' => '获取用户信息失败',
                    'data' => null
                ]);
            }

            // 4. 查找或创建用户
            $user = $this->findOrCreateUser($userInfo);
            if (!$user) {
                return json([
                    'code' => 500,
                    'message' => '用户创建失败',
                    'data' => null
                ]);
            }

            // 5. 生成JWT Token
            $jwtService = new JwtService();
            $token = $jwtService->createToken([
                'user_id' => $user['id'],
                'openid' => $openid
            ]);

            // 6. 记录登录日志
            $this->recordLoginLog($user['id']);

            // 7. 返回用户信息和token
            return json([
                'code' => 200,
                'message' => '登录成功',
                'data' => [
                    'token' => $token,
                    'userInfo' => [
                        'id' => $user['id'],
                        'openid' => $user['openid'],
                        'nickname' => $user['nickname'],
                        'avatar' => $user['avatar'],
                        'sex' => $user['sex'],
                        'province' => $user['province'],
                        'city' => $user['city'],
                        'country' => $user['country']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * 使用code获取access_token和openid
     * @param string $code
     * @return array|false
     */
    private function getWechatAccessToken($code)
    {
        // $appId = Config::get('wechat.official_account.default.app_id');
        // $appSecret = Config::get('wechat.official_account.default.secret');


        $url = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $params = [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $fullUrl = $url . '?' . http_build_query($params);
        $result = $this->httpGet($fullUrl);

        if ($result) {
            $data = json_decode($result, true);
            if (isset($data['access_token']) && isset($data['openid'])) {
                return $data;
            }

            // 记录错误日志
            \think\facade\Log::error('微信access_token获取失败: ' . json_encode($data));
        }

        return false;
    }

    /**
     * 获取微信用户信息
     * @param string $accessToken
     * @param string $openid
     * @return array|false
     */
    private function getWechatUserInfo($accessToken, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo";
        $params = [
            'access_token' => $accessToken,
            'openid' => $openid,
            'lang' => 'zh_CN'
        ];

        $fullUrl = $url . '?' . http_build_query($params);
        $result = $this->httpGet($fullUrl);

        if ($result) {
            $data = json_decode($result, true);
            if (isset($data['openid'])) {
                return $data;
            }

            // 记录错误日志
            \think\facade\Log::error('微信用户信息获取失败: ' . json_encode($data));
        }

        return false;
    }

    /**
     * 查找或创建用户
     * @param array $wechatUserInfo
     * @return array|false
     */
    private function findOrCreateUser($wechatUserInfo)
    {
        // 查找已存在的用户
        $user = UserModel::where('openid', $wechatUserInfo['openid'])->find();

        if ($user) {
            // 更新用户信息
            $updateData = [
                'nickname' => $wechatUserInfo['nickname'] ?? '',
                'avatar' => $wechatUserInfo['headimgurl'] ?? '',
                'sex' => $wechatUserInfo['sex'] ?? 0,
                'province' => $wechatUserInfo['province'] ?? '',
                'city' => $wechatUserInfo['city'] ?? '',
                'country' => $wechatUserInfo['country'] ?? '',
                'unionid' => $wechatUserInfo['unionid'] ?? '',
                'last_login_time' => time()
            ];

            $user->save($updateData);
            return $user->toArray();
        }

        // 创建新用户
        $userData = [
            'openid' => $wechatUserInfo['openid'],
            'unionid' => $wechatUserInfo['unionid'] ?? '',
            'nickname' => $wechatUserInfo['nickname'] ?? '',
            'avatar' => $wechatUserInfo['headimgurl'] ?? '',
            'sex' => $wechatUserInfo['sex'] ?? 0,
            'province' => $wechatUserInfo['province'] ?? '',
            'city' => $wechatUserInfo['city'] ?? '',
            'country' => $wechatUserInfo['country'] ?? '',
            'register_time' => time(),
            'last_login_time' => time(),
            'status' => 1
        ];

        $user = UserModel::create($userData);
        return $user ? $user->toArray() : false;
    }

    /**
     * 记录登录日志
     * @param int $userId
     * @return void
     */
    private function recordLoginLog($userId)
    {
        try {
            // 记录登录日志到数据库
            $loginLogData = [
                'user_id' => $userId,
                'login_ip' => request()->ip(),
                'login_time' => time(),
                'user_agent' => request()->header('user-agent'),
                'login_type' => 'wechat'
            ];

            // 这里假设您有登录日志模型
            // LoginLogModel::create($loginLogData);

            // 或者记录到缓存
            $cacheKey = 'user_login_' . $userId . '_' . date('Ymd');
            $loginCount = Cache::get($cacheKey, 0);
            Cache::set($cacheKey, $loginCount + 1, 86400);
        } catch (\Exception $e) {
            // 记录日志失败不影响主要流程
            \think\facade\Log::warning('记录登录日志失败: ' . $e->getMessage());
        }
    }

    /**
     * HTTP GET请求
     * @param string $url
     * @return string|false
     */
    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            \think\facade\Log::error('HTTP GET请求失败: ' . $error);
            return false;
        }

        return $response;
    }

    /**
     * 获取JSSDK配置（前端可能需要）
     * @param Request $request
     * @return Response
     */
    public function getJsConfig(Request $request)
    {
        try {
            $url = $request->param('url', '');
            if (empty($url)) {
                return json([
                    'code' => 400,
                    'message' => '缺少URL参数',
                    'data' => null
                ]);
            }

            $appId = Config::get('wechat.official_account.default.app_id');
            $appSecret = Config::get('wechat.official_account.default.secret');

            // 获取access_token
            $accessToken = $this->getGlobalAccessToken($appId, $appSecret);
            if (!$accessToken) {
                return json([
                    'code' => 500,
                    'message' => '获取access_token失败',
                    'data' => null
                ]);
            }

            // 获取jsapi_ticket
            $jsapiTicket = $this->getJsapiTicket($accessToken);
            if (!$jsapiTicket) {
                return json([
                    'code' => 500,
                    'message' => '获取jsapi_ticket失败',
                    'data' => null
                ]);
            }

            // 生成签名
            $nonceStr = $this->createNonceStr();
            $timestamp = time();
            $string = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
            $signature = sha1($string);

            return json([
                'code' => 200,
                'message' => '成功',
                'data' => [
                    'appId' => $appId,
                    'timestamp' => $timestamp,
                    'nonceStr' => $nonceStr,
                    'signature' => $signature,
                    'url' => $url
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * 获取全局access_token
     * @param string $appId
     * @param string $appSecret
     * @return string|false
     */
    private function getGlobalAccessToken($appId, $appSecret)
    {
        $cacheKey = 'wechat_access_token_' . $appId;

        // 尝试从缓存获取
        $accessToken = Cache::get($cacheKey);
        if ($accessToken) {
            return $accessToken;
        }

        // 从微信服务器获取
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appId,
            'secret' => $this->appSecret
        ];

        $fullUrl = $url . '?' . http_build_query($params);
        $result = $this->httpGet($fullUrl);

        if ($result) {
            $data = json_decode($result, true);
            if (isset($data['access_token'])) {
                // 缓存access_token，提前200秒过期
                $expireTime = $data['expires_in'] - 200;
                Cache::set($cacheKey, $data['access_token'], $expireTime);
                return $data['access_token'];
            }
        }

        return false;
    }

    /**
     * 获取jsapi_ticket
     * @param string $accessToken
     * @return string|false
     */
    private function getJsapiTicket($accessToken)
    {
        $cacheKey = 'wechat_jsapi_ticket';

        // 尝试从缓存获取
        $jsapiTicket = Cache::get($cacheKey);
        if ($jsapiTicket) {
            return $jsapiTicket;
        }

        // 从微信服务器获取
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket";
        $params = [
            'access_token' => $accessToken,
            'type' => 'jsapi'
        ];

        $fullUrl = $url . '?' . http_build_query($params);
        $result = $this->httpGet($fullUrl);

        if ($result) {
            $data = json_decode($result, true);
            if (isset($data['ticket'])) {
                // 缓存jsapi_ticket，提前200秒过期
                $expireTime = $data['expires_in'] - 200;
                Cache::set($cacheKey, $data['ticket'], $expireTime);
                return $data['ticket'];
            }
        }

        return false;
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取用户信息（通过openid）
     * @param Request $request
     * @return Response
     */
    public function getUserInfo(Request $request)
    {
        try {
            $openid = $request->param('openid', '');
            $token = $request->header('Authorization', '');

            if (empty($openid)) {
                return json([
                    'code' => 400,
                    'message' => '缺少openid参数',
                    'data' => null
                ]);
            }

            // 验证token
            $jwtService = new JwtService();
            if (!$jwtService->validateToken($token)) {
                return json([
                    'code' => 401,
                    'message' => '未授权访问',
                    'data' => null
                ]);
            }

            // 查询用户信息
            $user = UserModel::where('openid', $openid)->find();
            if (!$user) {
                return json([
                    'code' => 404,
                    'message' => '用户不存在',
                    'data' => null
                ]);
            }

            return json([
                'code' => 200,
                'message' => '成功',
                'data' => [
                    'userInfo' => [
                        'id' => $user['id'],
                        'openid' => $user['openid'],
                        'nickname' => $user['nickname'],
                        'avatar' => $user['avatar'],
                        'sex' => $user['sex'],
                        'province' => $user['province'],
                        'city' => $user['city'],
                        'country' => $user['country']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => '服务器错误: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
