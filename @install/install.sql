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
DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `image_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '图片id',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `tag` varchar(20) NOT NULL DEFAULT '' COMMENT 'tag',
  `sort` smallint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(1000) NOT NULL DEFAULT '' COMMENT '描述',
  `author` varchar(50) NOT NULL DEFAULT '' COMMENT '作者',
  `cover` varchar(100) NOT NULL DEFAULT '' COMMENT '封面',
  `cover_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '封面id',
  `view` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '阅读数',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `best` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签id',
  `tag_name` varchar(20) NOT NULL DEFAULT '' COMMENT '标签名字',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `count` int(4) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(20) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `tag` (`id`, `name`, `count`, `tag`, `sort`, `status`) VALUES(1,'笔记',0,'',0,1),(2,'其他',0,'',1,1);
DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '服务相对文件路径',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '本地上传文件名',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `size` varchar(50) NOT NULL DEFAULT '' COMMENT '文件大小byte单位',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `bind_type` varchar(10) NOT NULL DEFAULT '' COMMENT '上传服务类型',
  `bind_id` varchar(50) NOT NULL DEFAULT '0' COMMENT '上传服务关联id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(20) NOT NULL DEFAULT '' COMMENT '自定义账号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0普通会员 255 超级管理',
  `permission` varchar(255) NOT NULL DEFAULT '' COMMENT '权限',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `email_verification` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮箱是否验证',
  `tel` varchar(50) NOT NULL DEFAULT '' COMMENT '手机',
  `tel_verification` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机是否验证',
  `join_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '加入ip',
  `join_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '加入时间',
  `login_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '登陆ip',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `token` varchar(100) NOT NULL DEFAULT '' COMMENT '令牌',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;