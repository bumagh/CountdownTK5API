-- 用户表
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `unionid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信unionid',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0未知 1男 2女',
  `province` varchar(50) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '城市',
  `country` varchar(50) NOT NULL DEFAULT '' COMMENT '国家',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `register_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1正常',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_openid` (`openid`),
  KEY `idx_unionid` (`unionid`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- 用户登录日志表（可选）
CREATE TABLE `user_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `login_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '用户代理',
  `login_type` varchar(20) NOT NULL DEFAULT '' COMMENT '登录类型 wechat/account',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_login_time` (`login_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户登录日志表';