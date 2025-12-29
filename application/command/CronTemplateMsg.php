<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\service\CronTemplateMsg as CronService;

/**
 * 定时任务：倒数日前一天发送公众号模板消息
 */
class CronTemplateMsg extends Command
{
    protected function configure()
    {
        $this->setName('cron:templateMsg')
            ->setDescription('每天扫描明天到期的倒数日，并发送公众号模板消息提醒');
    }

    protected function execute(Input $input, Output $output)
    {
        $service = new CronService();
        $result = $service->run();

        $output->writeln(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return 0;
    }
}
