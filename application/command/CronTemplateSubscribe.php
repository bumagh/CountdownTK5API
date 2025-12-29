<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\service\CronTemplateSubscribe as CronService;

/**
 * 定时任务：倒数日前一天发送一次性订阅消息
 */
class CronTemplateSubscribe extends Command
{
    protected function configure()
    {
        // 命令名按默认：cron:templateSubscribe
        $this->setName('cron:templateSubscribe')
            ->setDescription('每天扫描明天到期的倒数日，并发送一次性订阅消息提醒');
    }

    protected function execute(Input $input, Output $output)
    {
        $service = new CronService();
        $result = $service->run();

        $output->writeln(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // 0 表示成功
        return 0;
    }
}
