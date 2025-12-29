<?php

namespace app\common\service;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;
use app\common\model\CountdownModel;
use app\common\model\UserModel;

/**
 * 定时任务：倒数日前一天发送一次性订阅消息
 */
class CronTemplateSubscribe
{
    /**
     * 扫描“明天”的倒数日并给用户推送一次性订阅消息。
     *
     * 说明：
     * - 本方法设计为被命令行/计划任务调用（例如每天 09:00 执行一次）
     * - 需要 users 表存在 openid 字段（否则无法推送）
     * - 使用 Cache 做每日去重：同一 countdown 同一天只推一次
     *
     * @param array $options
     *  - template_id: 订阅消息模板ID（必填；不传则读取 config/wechat.php 的 official_account.template_id）
     *  - scene: 场景值（默认 1000）
     *  - title: 标题（默认“订阅通知”）
     *  - url: 跳转链接（默认从配置读取，或 https://app.tutlab.tech/countdown/ ）
     *  - dry_run: true 时仅记录日志，不实际发送
     * @return array 汇总信息
     */
    public function run(array $options = [])
    {
        $templateId = (string)($options['template_id'] ?? Config::get('wechat.official_account.template_id', ''));
        $scene = (string)($options['scene'] ?? '1000');
        $title = (string)($options['title'] ?? '订阅通知');
        $url = (string)($options['url'] ?? (Config::get('wechat.official_account.url', '') ?: 'https://app.tutlab.tech/countdown/'));
        $dryRun = (bool)($options['dry_run'] ?? false);

        if ($templateId === '') {
            return ['ok' => false, 'message' => '缺少template_id（订阅消息模板ID）', 'sent' => 0, 'skipped' => 0, 'failed' => 0];
        }

        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // 只提醒未归档的
        $countdowns = CountdownModel::where('is_archived', false)
            ->where('date', $tomorrow)
            ->select();

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($countdowns as $cd) {
            $cdArr = $cd->toArray();
            $countdownId = (int)($cdArr['id'] ?? 0);
            $userId = (int)($cdArr['user_id'] ?? 0);

            // 每日去重（同一倒数日同一天只发一次）
            $dedupeKey = 'tpl_subscribe_sent_' . date('Ymd') . '_' . $countdownId;
            if (Cache::get($dedupeKey)) {
                $skipped++;
                continue;
            }

            $user = UserModel::where('id', $userId)->find();
            if (!$user) {
                $failed++;
                Log::warning('CronTemplateSubscribe: 用户不存在 user_id=' . $userId . ' countdown_id=' . $countdownId);
                continue;
            }

            $openid = trim((string)($user['openid'] ?? ''));
            if ($openid === '') {
                $failed++;
                Log::warning('CronTemplateSubscribe: 用户openid为空 user_id=' . $userId . ' countdown_id=' . $countdownId);
                continue;
            }

            $eventTitle = (string)($cdArr['title'] ?? '');
            $eventDate = (string)($cdArr['date'] ?? $tomorrow);

            // 订阅消息 data 结构：这里用 content 字段（按你的文档示例）
            // 注意：实际可用字段需与微信后台该 template_id 配置的“消息内容字段”一致。
            $data = [
                'content' => [
                    'value' => '提醒：' . ($eventTitle !== '' ? $eventTitle : '倒数日') . ' 将于明天（' . $eventDate . '）到来。',
                    'color' => '#FF0000'
                ]
            ];

            if ($dryRun) {
                Log::info('CronTemplateSubscribe dry_run: ' . json_encode([
                    'openid' => $openid,
                    'template_id' => $templateId,
                    'scene' => $scene,
                    'title' => $title,
                    'data' => $data,
                    'url' => $url,
                    'countdown_id' => $countdownId,
                ], JSON_UNESCAPED_UNICODE));

                Cache::set($dedupeKey, 1, 86400);
                $sent++;
                continue;
            }

            $respData = $this->sendSubscribe($openid, $templateId, $scene, $title, $data, $url);
            if (!is_array($respData)) {
                $failed++;
                continue;
            }

            if (($respData['errcode'] ?? -1) === 0) {
                Cache::set($dedupeKey, 1, 86400);
                $sent++;
            } else {
                $failed++;
            }
        }

        return [
            'ok' => true,
            'date' => $tomorrow,
            'total' => count($countdowns),
            'sent' => $sent,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }

    /**
     * 调用微信一次性订阅消息接口
     */
    private function sendSubscribe(string $openid, string $templateId, string $scene, string $title, array $data, string $url = '')
    {
        $appId = (string)Config::get('wechat.official_account.app_id', Config::get('wechat.official_account.default.app_id', ''));
        $appSecret = (string)Config::get('wechat.official_account.secret', Config::get('wechat.official_account.default.secret', ''));
        if ($appId === '' || $appSecret === '') {
            Log::error('CronTemplateSubscribe: 公众号appid/secret未配置');
            return false;
        }

        $accessToken = $this->getGlobalAccessToken($appId, $appSecret);
        if (!$accessToken) {
            Log::error('CronTemplateSubscribe: 获取access_token失败');
            return false;
        }

        $api = 'https://api.weixin.qq.com/cgi-bin/message/template/subscribe?access_token=' . $accessToken;
        $payload = [
            'touser' => $openid,
            'template_id' => $templateId,
            'scene' => $scene,
            'title' => $title,
            'data' => $data,
        ];
        if ($url !== '') {
            $payload['url'] = $url;
        }

        $resp = $this->httpPostJson($api, $payload);
        if ($resp === false) {
            Log::error('CronTemplateSubscribe: 订阅消息请求失败 openid=' . $openid);
            return false;
        }

        $respData = json_decode($resp, true);
        if (!is_array($respData)) {
            Log::error('CronTemplateSubscribe: 响应解析失败 resp=' . $resp);
            return false;
        }

        if (($respData['errcode'] ?? -1) !== 0) {
            Log::error('CronTemplateSubscribe: 发送失败 openid=' . $openid . ' resp=' . json_encode($respData, JSON_UNESCAPED_UNICODE));
        }

        return $respData;
    }

    private function getGlobalAccessToken(string $appId, string $appSecret)
    {
        $cacheKey = 'wechat_access_token_' . $appId;
        $accessToken = Cache::get($cacheKey);
        if ($accessToken) {
            return $accessToken;
        }

        // stable_token：POST JSON
        $url = 'https://api.weixin.qq.com/cgi-bin/stable_token';
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appId,
            'secret' => $appSecret,
        ];

        $result = $this->httpPostJson($url, $params);
        if (!$result) {
            return false;
        }

        $data = json_decode($result, true);
        if (isset($data['access_token'])) {
            $expireTime = (int)($data['expires_in'] ?? 7200) - 200;
            if ($expireTime < 60) {
                $expireTime = 60;
            }
            Cache::set($cacheKey, $data['access_token'], $expireTime);
            return $data['access_token'];
        }

        Log::error('CronTemplateSubscribe: access_token获取失败 resp=' . json_encode($data, JSON_UNESCAPED_UNICODE));
        return false;
    }

    private function httpPostJson(string $url, array $payload)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json)
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('CronTemplateSubscribe: HTTP POST失败: ' . $error);
            return false;
        }

        return $response;
    }
}
