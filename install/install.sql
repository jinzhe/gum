DROP TABLE IF EXISTS `link`;

 
CREATE TABLE `link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `image_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(20) NOT NULL,
  `sort` smallint(3) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO `link` (`id`, `title`, `image`,`image_id`, `url`, `tag`, `sort`, `time`, `status`) VALUES(1,'小林子','',0,'https://xlzi.cn','link',1,2147483647,1),(2,'傻瓜','',0,'http://shagua.name','link',2,1544092212,1),(3,'幸福彼岸','',0,'http://zee.kim','link',0,1555002780,1);

DROP TABLE IF EXISTS `post`;

CREATE TABLE `post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8 NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `author` varchar(50) CHARACTER SET utf8 NOT NULL,
  `cover` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `cover_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `view` int(4) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `best` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tag_name` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `tag` (`id`, `name`, `count`, `tag`, `sort`, `status`) VALUES(1,'笔记',0,'',0,1),(2,'其他',0,'',1,1);

DROP TABLE IF EXISTS `upload`;

CREATE TABLE `upload` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `size` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `bind_id` bigint(20) NOT NULL DEFAULT '0',
  `bind_type` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '自定义账号',
  `password` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0普通会员 255 超级管理',
  `nickname` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '昵称',
  `photo` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `email` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '邮箱',
  `email_verification` tinyint(1) NOT NULL DEFAULT '0',
  `tel` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机',
  `tel_verification` tinyint(1) NOT NULL DEFAULT '0',
  `join_ip` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `join_time` int(10) unsigned NOT NULL DEFAULT '0',
  `login_ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `token` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `user_verification`;

CREATE TABLE `user_verification` (
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:EMAIL,1:TEL',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT 'email认证码或者短信验证码',
  `time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否验证',
  `user_id` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;