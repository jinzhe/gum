<?php
require "../config.php";
require "../core/gum.php";
if (file::permissions(ROOT) != 15) {
    die('提示：“./” 无法写入');
}
if (file::permissions(ROOT . "upload") != 15) {
    die('提示：“upload”文件夹无法写入');
}
if (file::permissions(ROOT . "backup") != 15) {
    die('提示：“backup”文件夹无法写入');
}
if (file::permissions(ROOT . "config.php") != 15) {
    die('提示：“./config.php” 无法写入');
}

if ($_POST) {
    $db_host     = trim($_POST['db_host']);
    $db_port     = trim($_POST['db_port']);
    $db_user     = trim($_POST['db_user']);
    $db_password = trim($_POST['db_password']);
    $db_name     = trim($_POST['db_name']);
    $title       = trim($_POST['title']);
    $keywords    = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $icp         = trim($_POST['icp']);
    $copyright   = trim($_POST['copyright']);
    $domain      = trim($_POST['domain']);
    $account     = trim($_POST['account']);
    $password    = trim($_POST['password']);
    if ($db_name == "") {
        die("数据库不能为空");
    }
    if ($domain == "") {
        die("域名不能为空");
    }
    if ($title == "") {
        die("标题不能为空");
    }
    if ($account == "") {
        die("管理员不能为空");
    }
    if ($password == "") {
        die("管理员密码不能为空");
    }
    #数据库创建表
    $file = file_get_contents("./install.sql");
    if (!empty($file)) {
        $query = explode(";\n", $file);
        if (count($query) > 0) {
            $db = db::create("mysql", $db_name, $db_password, $db_user, $db_host, $db_port);
            foreach ($query as $sql) {
                if (!empty($sql)) {
                    $db->exec($sql);
                }
            }
        }
        $content = "<?php\n";
        $content .= "define('DEBUG', false);\n";
        $content .= "define('TIMEZONE', 'PRC'); //时区\n";
        $content .= "define('KEY', 'GUM'); //密钥，用于密码加密，如果更改会员密码都需要重新设置\n\n";

        $content .= "// 数据库\n";
        $content .= "define('DB_TYPE', 'mysql'); //MYSQL数据库\n";
        $content .= "define('DB_HOST', '$db_host');//数据库主机\n";
        $content .= "define('DB_PORT', $db_port); //端口\n";
        $content .= "define('DB_USER', '$db_user'); //用户\n";
        $content .= "define('DB_PASSWORD', '$db_password'); //密码\n";
        $content .= "define('DB_NAME', '$db_name'); //数据库\n\n";

        $content .= "// 邮件发送\n";
        $content .= "define('SMTP_SERVER', 'ssl://smtp.exmail.qq.com');\n";
        $content .= "define('SMTP_PORT', 465);\n";
        $content .= "define('SMTP_USER', '账号');\n";
        $content .= "define('SMTP_PASSWORD', '密码');\n";
        $content .= "define('SMTP_MAIL', '管理邮件地址');\n\n";

        $content .= "// 开关控制\n";
        $content .= "define('STATUS_SERVICE', true); //服务开关\n";
        $content .= "define('STATUS_LOGIN', true); //登陆开关\n";
        $content .= "define('STATUS_JOIN', true); //注册开关\n";
        $content .= "define('UPLOAD_IMAGE_OPACITY', 80); //上传图片质量 1～100\n";
        $content .= "define('DOMAIN', '$domain'); //发布域名\n";
        $content .= "define('TITLE', '$title'); //网站标题\n";
        $content .= "define('KEYWORDS', '$keywords'); //网站关键字\n";
        $content .= "define('DESCRIPTION', '$description'); //网站描述\n";
        $content .= "define('ICP', '$icp'); //备案号\n";
        $content .= "define('COPYRIGHT', '$copyright'); //版权信息\n";
        $content .= "define('THEME', 'light'); //默认主题\n";

        file_put_contents(ROOT . 'config.php', $content) or die("请检查文件 config.php 的权限是否为0777!");

        $db->insert("user", [
            "account"            => $account,
            "password"           => gum::hash($password),
            "level"              => 255,
            "nickname"           => "管理员",
            "photo"              => "",
            "email"              => "",
            "email_verification" => 0,
            "tel"                => "",
            "tel_verification"   => 0,
            "join_ip"            => gum::ip(),
            "join_time"          => time(),
            "login_ip"           => gum::ip(),
            "login_time"         => time(),
            "status"             => 1,
            "token"              => "",

        ]);
        die("安装成功，为保证安全请删除install文件夹");
    } else {
        die("请检查文件 ./install.sql 文件是否存在或者是否可读!");
    }

}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>GUM 安装程序</title>
<style>
*{
	box-sizing: border-box;
	outline:none;
}
html,body{
    margin:0;
    padding:0;


}
body{
    border-top:4px solid #418ce8;
    padding-bottom:100px;
}
.logo{
    padding:50px;
    text-align:center;
    font-size:50px;
    color:#418ce8;
}
form{

	margin:auto;
    display: flex;
    justify-content: center; /*水平对齐*/
    align-items: center; /*垂直对齐*/
    flex-direction:column;
	font-size:12px;
	text-align: center;
}
form div{
	margin-bottom: 10px;
}
form td{
    padding:10px;
}

input[type='text'],
input[type='password']{
    font-size:14px;
	padding:12px 20px;
	display: block;
	width:400px;
	border-radius: 50px;
	border:1px solid #ccc;

}
input[type='text']:focus,
input[type='password']:focus{
    border-color:#418ce8;
    box-shadow:0 0 0 3px rgba(65, 140, 232, .2);
}
button{
	width:200px;
    display: block;
    font-weight:bold;
	padding:10px 20px;
	border-radius: 50px;
	border:1px solid #418ce8;
	background-color:#418ce8;
	color:#fff;
}
.tip{
    padding-top:20px;
    text-align:left;
    color:#666;
}
</style>
<script>
function check(){
	if(confirm('确定要安装吗?')){
		return true;
	}
	return false;
}
</script>
</head>
<body>
<div class="logo">安装程序</div>
<form method="post">

<h3>数据库设置</h3>
	<div><input type="text" name="db_host" required value="127.0.0.1" placeholder="数据库主机" title="数据库主机"></div>
	<div><input type="text" name="db_port" required value="3306" placeholder="数据库端口" title="数据库端口"></div>
	<div><input type="text" name="db_user" required value="root" placeholder="数据库账号" title="数据库账号"></div>
	<div><input type="text" name="db_password" required  value="123456" placeholder="数据库密码" title="数据库密码"></div>
    <div><input type="text" name="db_name" required  value="" placeholder="数据库名称" title="数据库名称"></div>

<h3>基本设置</h3>

<div><input type="text" id="domain" required name="domain"  value="" placeholder="域名，例如：“https://zee.kim”" title="域名"></div>
<div><input type="text" id="title" required name="title"  value="" placeholder="网站标题，例如：“幸福彼岸”" title="网站标题"></div>
<div><input type="text" id="keywords" required name="keywords"  placeholder="网站关键字" title=""></div>
<div><input type="text" id="description" required name="description"  placeholder="网站描述" title=""></div>
<div><input type="text" id="icp" required name="icp"  value="" placeholder="ICP备案，例如：“蜀ICP备14010229号-3”" title="ICP备案"></div>
<div><input type="text" id="copyright" required name="copyright"  value="" placeholder="版权信息，例如：“ZEE.KIM”" title="版权信息"></div>

<h3>管理员设置</h3>

        <div><input type="text" id="account" required name="account"  value="" placeholder="管理账号" title="管理账号"></div>

    <div><input type="password" id="password" required name="password"  value="" placeholder="管理密码" title="管理密码"></div>



    <br><br>
	<button type="submit" id="button" onclick="this.innerText='安装中...';setTimeout(function(){button.innerText='开始安装'},3000)">开始安装</button>
<div class="tip">
    1.开始安装后会在数据库创建数据表，如果已存在相同表则会删除清空，请谨慎操作！<br>
    2.如果安装后还想修改配置，除了管理密码以外其他可以在config.php修改配置。<br>
    <!-- 3.程序安装后尽量不要修改密钥，否则所有密码加密都会失效，需要手动修改数据库。 -->
</div>
</form>

</body>
</html>