<?php

namespace app\common\service;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;
use app\common\model\CountdownModel;
use app\common\model\UserModel;

/**
 * 定时任务：倒数日前一天发送服务号模板消息（公众号模板消息）
 */
class CronTemplateMsg
{
    /**
     * 扫描“明天”的倒数日并给用户推送模板消息。
     *
     * @param array $options
     *  - template_id: 模板ID（不传则用 config/wechat.php 的 official_account.template_id）
     *  - url: 跳转链接（不传则用 config/wechat.php 的 official_account.url）
     *  - miniprogram: 跳小程序配置（可选）
     *  - dry_run: true 时仅记录日志，不实际发送
     */
    public function run(array $options = [])
    {
        $templateId = (string)($options['template_id'] ?? Config::get('wechat.official_account.template_id', ''));
        $url = (string)($options['url'] ?? Config::get('wechat.official_account.url', ''));
        $miniprogram = $options['miniprogram'] ?? Config::get('wechat.official_account.miniprogram', []);
        $dryRun = (bool)($options['dry_run'] ?? false);

        if ($templateId === '') {
            return ['ok' => false, 'message' => '缺少template_id（模板ID）', 'sent' => 0, 'skipped' => 0, 'failed' => 0];
        }

        $tomorrow = date('Y-m-d', strtotime('+1 day'));

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

            // 每日去重：同一倒数日同一天只发一次
            $dedupeKey = 'tpl_msg_sent_' . date('Ymd') . '_' . $countdownId;
            if (Cache::get($dedupeKey)) {
                $skipped++;
                continue;
            }

            $user = UserModel::where('id', $userId)->find();
            if (!$user) {
                $failed++;
                Log::warning('CronTemplateMsg: 用户不存在 user_id=' . $userId . ' countdown_id=' . $countdownId);
                continue;
            }
            if ($user['serviceno_notice'] == 0) {
                $skipped++;
                continue;
            }
            $openid = trim((string)($user['openid'] ?? ''));
            if ($openid === '') {
                $failed++;
                Log::warning('CronTemplateMsg: 用户openid为空 user_id=' . $userId . ' countdown_id=' . $countdownId);
                continue;
            }

            $eventTitle = (string)($cdArr['title'] ?? '倒数日');
            $eventDate = (string)($cdArr['date'] ?? $tomorrow);

            // data 结构需要与你的公众号模板字段一致。
            // 这里给一个通用示例：first + keyword1 + keyword2
            $data = [
                'time1' => ['value' => $eventDate, 'color' => '#173177'],
                'thing3' => ['value' => $user['nickname'], 'color' => '#173177'],
                'thing5' => ['value' => $eventTitle, 'color' => '#173177'],
            ];

            if ($dryRun) {
                Log::info('CronTemplateMsg dry_run: ' . json_encode([
                    'openid' => $openid,
                    'template_id' => $templateId,
                    'data' => $data,
                    'url' => $url,
                    // 'miniprogram' => $miniprogram,
                    'countdown_id' => $countdownId,
                ], JSON_UNESCAPED_UNICODE));

                Cache::set($dedupeKey, 1, 86400);
                $sent++;
                continue;
            }

            $respData = $this->sendTemplateMessage($openid, $templateId, $data, $url, $miniprogram);
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

    private function sendTemplateMessage(string $openid, string $templateId, array $data, string $url = '', $miniprogram = [])
    {
        $appId = (string)Config::get('wechat.official_account.app_id', Config::get('wechat.official_account.default.app_id', ''));
        $appSecret = (string)Config::get('wechat.official_account.secret', Config::get('wechat.official_account.default.secret', ''));
        if ($appId === '' || $appSecret === '') {
            Log::error('CronTemplateMsg: 公众号appid/secret未配置');
            return false;
        }

        $accessToken = $this->getGlobalAccessToken($appId, $appSecret);
        if (!$accessToken) {
            Log::error('CronTemplateMsg: 获取access_token失败');
            return false;
        }

        $api = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        $payload = [
            'touser' => $openid,
            'template_id' => $templateId,
            'data' => $data,
        ];
        if ($url !== '') {
            $payload['url'] = $url;
        }
        if (is_array($miniprogram) && !empty($miniprogram['appid']) && !empty($miniprogram['pagepath'])) {
            $payload['miniprogram'] = [
                'appid' => $miniprogram['appid'],
                'pagepath' => $miniprogram['pagepath'],
            ];
        }

        $resp = $this->httpPostJson($api, $payload);
        if ($resp === false) {
            Log::error('CronTemplateMsg: 模板消息请求失败 openid=' . $openid);
            return false;
        }

        $respData = json_decode($resp, true);
        if (!is_array($respData)) {
            Log::error('CronTemplateMsg: 响应解析失败 resp=' . $resp);
            return false;
        }

        if (($respData['errcode'] ?? -1) !== 0) {
            Log::error('CronTemplateMsg: 发送失败 openid=' . $openid . ' resp=' . json_encode($respData, JSON_UNESCAPED_UNICODE));
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

        Log::error('CronTemplateMsg: access_token获取失败 resp=' . json_encode($data, JSON_UNESCAPED_UNICODE));
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
            Log::error('CronTemplateMsg: HTTP POST失败: ' . $error);
            return false;
        }

        return $response;
    }
}
