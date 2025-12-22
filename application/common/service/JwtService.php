<?php
// app/common/service/JwtService.php

namespace app\common\service;

use think\facade\Config;
use think\facade\Cache;

class JwtService
{
    private $secret;
    private $algorithm;
    private $expire;
    
    public function __construct()
    {
        $config = Config::get('jwt', []);
        $this->secret = $config['secret'] ?? 'your-secret-key-change-this';
        $this->algorithm = $config['algorithm'] ?? 'HS256';
        $this->expire = $config['expire'] ?? 7200; // 默认2小时
    }
    
    /**
     * 创建Token
     * @param array $payload
     * @return string
     */
    public function createToken($payload)
    {
        $header = [
            'alg' => $this->algorithm,
            'typ' => 'JWT'
        ];
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expire;
        
        $headerBase64 = $this->base64UrlEncode(json_encode($header));
        $payloadBase64 = $this->base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', "$headerBase64.$payloadBase64", $this->secret, true);
        $signatureBase64 = $this->base64UrlEncode($signature);
        
        $token = "$headerBase64.$payloadBase64.$signatureBase64";
        
        // 缓存token
        $cacheKey = 'user_token_' . md5($token);
        Cache::set($cacheKey, $payload, $this->expire);
        
        return $token;
    }
    
    /**
     * 验证Token
     * @param string $token
     * @return array|false
     */
    public function validateToken($token)
    {
        if (empty($token)) {
            return false;
        }
        
        // 检查缓存
        $cacheKey = 'user_token_' . md5($token);
        if (!Cache::has($cacheKey)) {
            return false;
        }
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        list($headerBase64, $payloadBase64, $signatureBase64) = $parts;
        
        // 验证签名
        $signature = $this->base64UrlDecode($signatureBase64);
        $expectedSignature = hash_hmac('sha256', "$headerBase64.$payloadBase64", $this->secret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        $payload = json_decode($this->base64UrlDecode($payloadBase64), true);
        
        // 检查过期时间
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            Cache::delete($cacheKey);
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Base64 URL编码
     * @param string $data
     * @return string
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL解码
     * @param string $data
     * @return string
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * 刷新Token
     * @param string $token
     * @return string|false
     */
    public function refreshToken($token)
    {
        $payload = $this->validateToken($token);
        if (!$payload) {
            return false;
        }
        
        // 删除旧的缓存
        $oldCacheKey = 'user_token_' . md5($token);
        Cache::delete($oldCacheKey);
        
        // 移除过期时间，创建新的token
        unset($payload['iat'], $payload['exp']);
        return $this->createToken($payload);
    }
}