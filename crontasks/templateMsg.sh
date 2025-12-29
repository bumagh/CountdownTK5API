#!/usr/bin/env bash
set -euo pipefail

# 进入项目根目录（脚本位于 crontasks/ 下）
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${PROJECT_ROOT}"

# 使用环境变量 PHP_BIN 指定 php 路径；如未设置则默认 php
PHP_BIN="${PHP_BIN:-php}"

# 输出日志目录（ThinkPHP runtime/log 存在即可）
LOG_DIR="${PROJECT_ROOT}/runtime/log"
mkdir -p "${LOG_DIR}"
LOG_FILE="${LOG_DIR}/cron_template_msg.log"

# 执行模板消息定时任务
# 说明：crontab 建议配置为：30 21 * * * /bin/bash /path/to/CountdownTK5API/crontasks/templateMsg.sh
"${PHP_BIN}" think cron:templateMsg >> "${LOG_FILE}" 2>&1
