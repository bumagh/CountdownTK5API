CREATE TABLE
  `version` (
    `id` int (11) UNSIGNED PRIMARY KEY NOT NULL,
    `version` varchar(255) DEFAULT NULL,
    `update_method` varchar(255) DEFAULT NULL,
    `update_rule` varchar(255) DEFAULT NULL,
    `notice_url` varchar(255) DEFAULT NULL,
    `apkurl` varchar(255) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `create_time` TIMESTAMP DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8;

--
-- 转存表中的数据 `version`
--
INSERT INTO
  `version` (
    `id`,
    `version`,
    `update_method`,
    `update_rule`,
    `notice_url`,
    `apkurl`,
    `title`,
    `description`,
    `create_time`
  )
VALUES
  (
    1,
    '0.0.1',
    '',
    '',
    '',
    'https://cos.tutlab.tech/sgol_0.1.5.apk',
    '标题',
    '0.1.5 版本描述',
    NULL
  );

-- users中增加服务号提醒功能
ALTER TABLE `users`
ADD COLUMN `serviceno_notice` TINYINT (1) DEFAULT 1 AFTER `status`;