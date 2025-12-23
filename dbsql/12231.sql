-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.7.40 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- 导出 countdownapp 的数据库结构
CREATE DATABASE IF NOT EXISTS `countdownapp` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `countdownapp`;

-- 导出  存储过程 countdownapp.AddCountdown 结构
DELIMITER //
CREATE PROCEDURE `AddCountdown`(
    IN p_title VARCHAR(200),
    IN p_date DATE,
    IN p_category_id INT,
    IN p_user_id INT,
    IN p_is_pinned BOOLEAN,
    IN p_repeat_cycle INT,
    IN p_repeat_frequency ENUM('不重复', '天重复', '周重复', '月重复', '年重复')
)
BEGIN
    INSERT INTO countdowns (title, date, category_id, user_id, is_pinned, repeat_cycle, repeat_frequency)
    VALUES (p_title, p_date, p_category_id, p_user_id, p_is_pinned, p_repeat_cycle, p_repeat_frequency);
    
    SELECT LAST_INSERT_ID() as new_id;
END//
DELIMITER ;

-- 导出  表 countdownapp.admin 结构
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT '0',
  `update_time` int(11) unsigned DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `zone_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `admin_zone_id` (`zone_id`),
  CONSTRAINT `admin_zone_id` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- 正在导出表  countdownapp.admin 的数据：~13 rows (大约)
INSERT INTO `admin` (`id`, `username`, `password`, `create_time`, `update_time`, `status`, `zone_id`) VALUES
	(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 0, 1736925349, 1, 1),
	(2, 'gsj', 'e10adc3949ba59abbe56e057f20f883e', 0, 0, 1, NULL),
	(11, 'test1', '202cb962ac59075b964b07152d234b70', 1736527660, 1736527660, NULL, NULL),
	(12, 'test2', '202cb962ac59075b964b07152d234b70', 1736527913, 1736527913, NULL, NULL),
	(13, 'test3', '202cb962ac59075b964b07152d234b70', 1736527926, 1736527926, NULL, NULL),
	(14, 'test7', 'e10adc3949ba59abbe56e057f20f883e', 1736575990, 1736575990, 1, NULL),
	(15, 'test8', '202cb962ac59075b964b07152d234b70', 1736576138, 1736576138, 1, NULL),
	(16, 'test9', '202cb962ac59075b964b07152d234b70', 1736576307, 1736603239, 1, 1),
	(17, 'test02', '202cb962ac59075b964b07152d234b70', 0, 0, 1, NULL),
	(18, 'test03', '202cb962ac59075b964b07152d234b70', 0, 1736929261, 1, 1),
	(19, 'test04', '202cb962ac59075b964b07152d234b70', 0, 1736929299, 1, 1),
	(20, 'test05', '202cb962ac59075b964b07152d234b70', 0, 1736930069, 1, 1),
	(21, 'test06', '202cb962ac59075b964b07152d234b70', 0, 1736942407, 1, 1);

-- 导出  表 countdownapp.admin_role 结构
CREATE TABLE IF NOT EXISTS `admin_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `role_id` int(11) unsigned DEFAULT NULL,
  `status` int(11) unsigned DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_role_admin_id` (`admin_id`),
  KEY `admin_role_role_id` (`role_id`),
  CONSTRAINT `admin_role_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `admin_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 正在导出表  countdownapp.admin_role 的数据：~2 rows (大约)
INSERT INTO `admin_role` (`id`, `admin_id`, `role_id`, `status`, `create_time`) VALUES
	(1, 1, 1, 1, '2025-01-10 22:18:11'),
	(2, 2, 2, 1, '2025-01-10 22:19:40');

-- 导出  表 countdownapp.categories 结构
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_categories_user` (`user_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 正在导出表  countdownapp.categories 的数据：~4 rows (大约)
INSERT INTO `categories` (`id`, `name`, `icon`, `color`, `user_id`, `created_at`, `updated_at`) VALUES
	(42, 'work', '💼', '#52c41a', 28, '2025-12-23 12:37:30', '2025-12-23 12:37:30'),
	(43, 'family', '👨‍👩‍👧', '#faad14', 28, '2025-12-23 12:37:30', '2025-12-23 12:37:30'),
	(44, 'life', '🏠', '#1890ff', 28, '2025-12-23 12:37:30', '2025-12-23 12:37:30'),
	(45, 'longlife', '❤️', '#f5222d', 28, '2025-12-23 12:37:30', '2025-12-23 12:37:30'),
	(46, 'test', '🏠', '#ff6b9d', 28, '2025-12-23 12:59:06', '2025-12-23 12:59:06');

-- 导出  表 countdownapp.countdowns 结构
CREATE TABLE IF NOT EXISTS `countdowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_pinned` tinyint(1) DEFAULT '0',
  `repeat_cycle` int(11) DEFAULT '0',
  `repeat_frequency` enum('不重复','天重复','周重复','月重复','年重复') DEFAULT '不重复',
  `is_archived` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `idx_user_category` (`user_id`,`category_id`),
  KEY `idx_date` (`date`),
  KEY `idx_pinned_archived` (`is_pinned`,`is_archived`),
  KEY `idx_countdowns_user_archived` (`user_id`,`is_archived`),
  KEY `idx_countdowns_user_pinned` (`user_id`,`is_pinned`),
  CONSTRAINT `countdowns_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `countdowns_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- 正在导出表  countdownapp.countdowns 的数据：~2 rows (大约)
INSERT INTO `countdowns` (`id`, `title`, `date`, `category_id`, `user_id`, `is_pinned`, `repeat_cycle`, `repeat_frequency`, `is_archived`, `created_at`, `updated_at`) VALUES
	(17, '120岁倒数日', '2145-12-23', 45, 28, 1, 0, '不重复', 0, '2025-12-23 12:37:30', '2025-12-23 12:37:30'),
	(26, 'a', '2025-12-24', 42, 28, 0, 0, '不重复', 0, '2025-12-23 13:48:53', '2025-12-23 13:48:53');

-- 导出  表 countdownapp.merge 结构
CREATE TABLE IF NOT EXISTS `merge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `itemaid` varchar(255) NOT NULL,
  `itembid` varchar(255) NOT NULL,
  `newitemid` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `level` double NOT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- 正在导出表  countdownapp.merge 的数据：~35 rows (大约)
INSERT INTO `merge` (`id`, `ip`, `itemaid`, `itembid`, `newitemid`, `type`, `level`, `update_time`) VALUES
	(1, '1.1.1.1.1', '1', '1', '2', '香蕉', 2, '2025-11-19 03:22:55'),
	(2, '2.2.2.2', '3', '3', '4', '草莓', 3, '2025-11-19 06:29:10'),
	(3, '115.150.123.224', 'fruit_mi5hgrxb_jrawq8mif', 'fruit_mi5hgsaq_h4jdyxjmv', 'fruit_mi5hgtlx_r9fsn0qw9', 'halfwatermelon', 2, '2025-11-19 06:29:03'),
	(5, '115.150.123.224', 'fruit_mi5mo1su_t6syf2nfl', 'fruit_mi5mo26j_2ergllf6j', 'fruit_mi5mo3as_b24fo5qp3', 'halfwatermelon', 2, '2025-11-19 06:35:00'),
	(6, '115.150.123.224', 'fruit_mi5msglc_usnmhwkop', 'fruit_mi5my7sk_r5ny4iigr', 'fruit_mi5nd72t_zvrfi1c93', 'halfwatermelon', 2, '2025-11-19 06:54:32'),
	(7, '115.150.123.224', 'fruit_mi5my350_793bk65kh', 'fruit_mi5mq5di_pqxz8a6di', 'fruit_mi5nd7y7_i1kjd2qhy', 'halfwatermelon', 2, '2025-11-19 06:54:34'),
	(8, '115.150.123.224', 'fruit_mi5mq9g9_x6o7mtv0v', 'fruit_mi5my5gs_j265ltzck', 'fruit_mi5ndagz_5113rwv6z', 'halfwatermelon', 2, '2025-11-19 06:54:35'),
	(9, '115.150.123.224', 'fruit_mi5mxzn3_57zvyzp54', 'fruit_mi5mo443_7jpb1y2ri', 'fruit_mi5ndbbm_ew1k234z8', 'halfwatermelon', 2, '2025-11-19 06:54:37'),
	(10, '115.150.123.224', 'fruit_mi5nsuca_rzq5685eq', 'fruit_mi5nsups_10scuf38m', 'fruit_mi5nswje_x17i96huz', 'halfwatermelon', 2, '2025-11-19 07:06:45'),
	(11, '115.150.123.224', 'fruit_mi5nt1a9_cxrhacxus', 'fruit_mi5nswn4_7n5r691pp', 'fruit_mi5ntgwo_m1l5aftd1', 'halfwatermelon', 2, '2025-11-19 07:07:10'),
	(12, '115.150.123.224', 'fruit_mi5ntgwo_m1l5aftd1', 'fruit_mi5nswje_x17i96huz', 'fruit_mi5ntkki_03hfceb90', 'kiwi', 3, '2025-11-19 07:07:16'),
	(13, '115.150.123.224', 'fruit_mi5nthhl_fz08ca56c', 'fruit_mi5nsyyg_kpkezfwtz', 'fruit_mi5ntlw8_kt0xjlom4', 'halfwatermelon', 2, '2025-11-19 07:07:17'),
	(14, '115.150.123.224', 'fruit_mi5ntm49_wvmg0n7io', 'fruit_mi5ntjsy_z7s3j77xk', 'fruit_mi5ntnma_m85ii6z2d', 'halfwatermelon', 2, '2025-11-19 07:07:19'),
	(15, '127.0.0.1', 'fruit_mi5nzs75_t80456bpn', 'fruit_mi5nznxf_0gg55lsw8', 'fruit_mi5nzvev_5jxf4z3s4', 'halfwatermelon', 2, '2025-11-19 07:12:08'),
	(16, '127.0.0.1', 'fruit_mi5nznk2_8cbt42kek', 'fruit_mi5nzpvd_1gdt7fqz0', 'fruit_mi5nzx2n_x5d5l2jfi', 'halfwatermelon', 2, '2025-11-19 07:12:10'),
	(17, '127.0.0.1', 'fruit_mi5o0g2y_tw2evlory', 'fruit_mi5o0ggr_pnl910yh6', 'fruit_mi5o0hor_w5uavatm3', 'halfwatermelon', 2, '2025-11-19 07:12:37'),
	(18, '127.0.0.1', 'fruit_mi5o0idr_71k7l5rks', 'fruit_mi5o0kp2_vgo4eveak', 'fruit_mi5o0lmc_7m5hsulop', 'halfwatermelon', 2, '2025-11-19 07:12:42'),
	(19, '127.0.0.1', 'fruit_mi5o0hor_w5uavatm3', 'fruit_mi5o0lmc_7m5hsulop', 'fruit_mi5o0o56_u5v97uabz', 'kiwi', 3, '2025-11-19 07:12:45'),
	(20, '127.0.0.1', 'fruit_mi5o0n0v_3fjp9xebz', 'fruit_mi5o0pcn_37yglrtjy', 'fruit_mi5o0q70_2t3rl5nc9', 'halfwatermelon', 2, '2025-11-19 07:12:48'),
	(21, '127.0.0.1', 'fruit_mi5o0rnz_brlmmcrid', 'fruit_mi5o0tzt_9lyjv1bs3', 'fruit_mi5o0vkk_549ikrkeo', 'halfwatermelon', 2, '2025-11-19 07:12:55'),
	(22, '127.0.0.1', 'fruit_mi5o0q70_2t3rl5nc9', 'fruit_mi5o0vkk_549ikrkeo', 'fruit_mi5o100i_wrmt60h7o', 'kiwi', 3, '2025-11-19 07:13:01'),
	(23, '127.0.0.1', 'fruit_mi5o100i_wrmt60h7o', 'fruit_mi5o0o56_u5v97uabz', 'fruit_mi5o1176_fisqq5zm5', 'lemon', 4, '2025-11-19 07:13:02'),
	(24, '127.0.0.1', 'fruit_mi5o0wb5_8zrshoj6c', 'fruit_mi5o10y9_b5h4gx7ar', 'fruit_mi5o126z_02z459i0w', 'halfwatermelon', 2, '2025-11-19 07:13:03'),
	(25, '127.0.0.1', 'fruit_mi5o139l_g4s2p3mcu', 'fruit_mi5o0ymx_8f24l1kp0', 'fruit_mi5o15pv_haycaajli', 'halfwatermelon', 2, '2025-11-19 07:13:08'),
	(26, '127.0.0.1', 'fruit_mi5o15le_f9uqm7vy5', 'fruit_mi5o17wq_tqquw4wkv', 'fruit_mi5o18ux_5tu34umm9', 'halfwatermelon', 2, '2025-11-19 07:13:12'),
	(27, '127.0.0.1', 'fruit_mi5o18ux_5tu34umm9', 'fruit_mi5o15pv_haycaajli', 'fruit_mi5o19t5_dtzsbso26', 'kiwi', 3, '2025-11-19 07:13:13'),
	(28, '127.0.0.1', 'fruit_mi5o1ckb_jb477hrv1', 'fruit_mi5o1a8i_c9wh7wu9i', 'fruit_mi5o1e8s_x3ekbwz0t', 'halfwatermelon', 2, '2025-11-19 07:13:19'),
	(29, '127.0.0.1', 'fruit_mi5o126z_02z459i0w', 'fruit_mi5o1e8s_x3ekbwz0t', 'fruit_mi5o1fdk_0o37lsn89', 'kiwi', 3, '2025-11-19 07:13:20'),
	(30, '127.0.0.1', 'fruit_mi5o1fdk_0o37lsn89', 'fruit_mi5o19t5_dtzsbso26', 'fruit_mi5o1g57_pi6y3l9z8', 'lemon', 4, '2025-11-19 07:13:21'),
	(31, '127.0.0.1', 'fruit_mi5o1g57_pi6y3l9z8', 'fruit_mi5o1176_fisqq5zm5', 'fruit_mi5o1gwy_wwrbez633', 'orange', 5, '2025-11-19 07:13:22'),
	(32, '127.0.0.1', 'fruit_mi5o1ew4_pbpvn3r51', 'fruit_mi5o1h7g_rwu6hwbon', 'fruit_mi5o1i6a_csb0b3gvd', 'halfwatermelon', 2, '2025-11-19 07:13:24'),
	(33, '127.0.0.1', 'fruit_mi5o1lu4_ci3yqiyog', 'fruit_mi5o1jis_q88q508xo', 'fruit_mi5o1mhk_3fpqa3pkv', 'halfwatermelon', 2, '2025-11-19 07:13:30'),
	(34, '127.0.0.1', 'fruit_mi5o1i6a_csb0b3gvd', 'fruit_mi5o1mhk_3fpqa3pkv', 'fruit_mi5o1nmr_p4o9kfwmf', 'kiwi', 3, '2025-11-19 07:13:31'),
	(35, '127.0.0.1', 'fruit_mi5o1qhp_ll8aka6ih', 'fruit_mi5o1o5w_5362q6tp2', 'fruit_mi5o1rmh_tps70dj6f', 'halfwatermelon', 2, '2025-11-19 07:13:36'),
	(36, '127.0.0.1', 'fruit_mi5o5xfy_0sla6j83r', 'fruit_mi5o5t76_dbhu50z00', 'fruit_mi5o5yg0_fzwshey18', 'halfwatermelon', 2, '2025-11-19 07:16:52');

-- 导出  表 countdownapp.role 结构
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 正在导出表  countdownapp.role 的数据：~2 rows (大约)
INSERT INTO `role` (`id`, `name`, `status`, `create_time`, `update_time`) VALUES
	(1, '超级管理员', 1, 0, 0),
	(2, '玩家', 1, 0, 0);

-- 导出  表 countdownapp.role_rule 结构
CREATE TABLE IF NOT EXISTS `role_rule` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `rule_ids` text,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 正在导出表  countdownapp.role_rule 的数据：~0 rows (大约)

-- 导出  表 countdownapp.rule 结构
CREATE TABLE IF NOT EXISTS `rule` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- 正在导出表  countdownapp.rule 的数据：~3 rows (大约)
INSERT INTO `rule` (`id`, `name`, `pid`, `img`, `status`, `create_time`, `update_time`, `url`) VALUES
	(1, '管理员功能', 0, NULL, 1, 1735874408, 1735874408, 'admin/index'),
	(2, '编辑管理员', 1, NULL, 1, 1735874408, 1735874408, 'admin/save'),
	(3, '删除管理员', 1, 'test1', 1, 1736156143, 1736156564, 'admin/delete');

-- 导出  表 countdownapp.users 结构
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) NOT NULL DEFAULT '倒数日用户',
  `avatar` varchar(500) DEFAULT 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- 正在导出表  countdownapp.users 的数据：~2 rows (大约)
INSERT INTO `users` (`id`, `nickname`, `avatar`, `created_at`, `updated_at`, `password`, `username`, `birth_date`) VALUES
	(1, '张三1', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop', '2025-11-27 04:38:15', '2025-12-15 12:43:27', '123456', 'user1', NULL),
	(28, '倒数日用户', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop', '2025-12-23 12:37:30', '2025-12-23 12:37:30', '123456', 't2302', '2025-12-23');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
