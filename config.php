<?php
define('DEBUG', true);
define('OPEN', true);
define('TIMEZONE', 'PRC');
define('KEY', 'GUM'); // 用于哈希密码生成
define('DOMAIN', 'http://gum'); // 发布域名

// 数据库
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASSWORD', '123456');
define('DB_NAME', 'cms');

// 邮件发送
define('SMTP_SERVER', 'ssl://smtp.exmail.qq.com');
define('SMTP_PORT', 465);
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');
define('SMTP_MAIL', '');

// 上传相关
define('UPLOAD_IMAGE_OPACITY', 80); //图片质量 1～100