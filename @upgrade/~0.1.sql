ALTER TABLE `upload` CHANGE `bind_id` `bind_id` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
ALTER TABLE `user` ADD `permission` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `level`;
UPDATE `user` SET permission='super' WHERE level=255;

DROP TABLE IF EXISTS `user_verification`;
DROP TABLE IF EXISTS `user_code`;

CREATE TABLE `user_code` (
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:EMAIL,1:TEL',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT 'email认证码或者短信验证码',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '验证时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否验证',
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '关联用户id'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `email` varchar(200) NOT NULL DEFAULT '' COMMENT '邮箱',
  `content` varchar(5000) NOT NULL DEFAULT '' COMMENT '评论内容',
  `reply` varchar(1000) NOT NULL DEFAULT '' COMMENT '管理员回复内容',
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'ip',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '评论时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `bind_type` varchar(20) NOT NULL DEFAULT '' COMMENT '评论服务类型',
  `bind_value` varchar(255) NOT NULL DEFAULT '' COMMENT '评论服务关联值',
  `parent_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '父评论id',
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:文本，1:富文本，2:上传文件，4:开关',
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT '变量',
  `value` text NOT NULL COMMENT '值',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `config` (`type`, `key`, `value`, `description`, `status`)
VALUES
	(0,'title','幸福彼岸','标题',1),
	(0,'keywords','GUM','SEO关键字',1),
	(0,'description','GUM是一个PHP程序，一般用它来做博客，但是通过二次开发也可以做其他类型的网站。','SEO描述',1),
	(0,'icp','蜀ICP备xxxxxx号','备案号',1),
	(0,'copyright','ZEE.KIM','版权',1),
	(2,'about-avatar','','关于头像',1),
	(1,'about-content','','关于内容',1);