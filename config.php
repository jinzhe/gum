<?php
define('DEBUG', false);
define('TIMEZONE', 'PRC'); //时区
define('KEY', 'GUM'); //密钥，用于密码加密，如果更改会员密码都需要重新设置

// 数据库
define('DB_TYPE', 'mysql'); //MYSQL数据库
define('DB_HOST', '127.0.0.1');//数据库主机
define('DB_PORT', 3306); //端口
define('DB_USER', 'root'); //用户
define('DB_PASSWORD', '123456'); //密码
define('DB_NAME', 'xxxxx'); //数据库

// 邮件发送
define('SMTP_SERVER', 'ssl://smtp.exmail.qq.com');
define('SMTP_PORT', 465);
define('SMTP_USER', '账号');
define('SMTP_PASSWORD', '密码');
define('SMTP_MAIL', '管理邮件地址');

// 开关控制
define('STATUS_SERVICE', true); //服务开关
define('STATUS_LOGIN', true); //登陆开关
define('STATUS_JOIN', true); //注册开关
define('UPLOAD_IMAGE_OPACITY', 80); //上传图片质量 1～100
define('DOMAIN', 'https://zee.kim'); //发布域名
define('TITLE', 'xxxxx'); //站点标题
define('ICP', 'xxxxx'); //备案号
define('COPYRIGHT', 'xxxxx'); //版权信息
define('THEME', 'light'); //默认主题
