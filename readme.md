# 概览
> gum是基于php的开箱即用代码库。

# 基本功能
```php
// 初始化页面
gum::init([
	"headers" => [
		"Access-Control-Allow-Origin: *",
	],
	"session" => true
]);

// 获取get post 传递的参数
gum::query("id"); //参数为空则取$_SERVER["QUERY_STRING"];

//对称加密解密
gum::encode("str");
gum::decode("str");

//发送stmp邮件

$success = gum::mail([
	"to" => "发送地址",
	"subject" => "邮件标题",
	"body" => "邮件内容",
	"from" => SMTP_USER,
	"server" => SMTP_SERVER,
	"port" => SMTP_PORT,
	"user" => SMTP_USER,
	"password" => SMTP_PASSWORD,
]);
if ($success) {
	gum::json(["code" => 200]);
} else {
	gum::json(["code" => 404]);
}

// 随机字符串
gum::randomCode(6);

// uuid 生成
gum::uuid();

//输出json 格式化默认JSON_NUMERIC_CHECK，如想自定义传第二个参数即可
gum::json(["success"=>true]);

// http请求
gum::fetch("http://baidu.com");

// 获取ip屋里地址
gum::ipAddress("115.29.43.145");

// 获取当前路径
gum::path();

// 获取当前运行位置
gum::base();

// 获取ip地址
gum::ip();

// 获取操作系统
gum::os();

// 是否是搜索引擎
gum::isSpider();

// 判断是否为移动设备
gum::isMobile();

// 404
gum::notFound("可自定义输出内容");

// 301永久重定向
gum::redirect("https://im.zee.kim");
```


# 数据库
```php
//创建数据库并连接(一般安装程序用)
$db = db::create("mysql", "table","password","root","127.0.0.1",3306);

// 链接数据库
$db = new db("mysql","table","password","root","127.0.0.1",3306);//mysql
$db = new db("sqlite","./file.db");//sqlite

// 查询一条记录
$row=$db->row("SELECT * FROM table");

// 查询记录集合
$rows=$db->rows("SELECT * FROM table");

// 查询记录总数
$count=$db->count("SELECT * FROM table");

// 插入
$db->insert("table",[
	"title" => "title",
	"content" => "content",
]);

// 获取新插入的自动编号id
$db->id();

// 更新
$db->update("table",[
	"title" => "title",
	"content" => "content",
],"id=1");

// 删除
$db->delete("table","id=1");

// 清空表数据
$db->clear(["table1","table2"]);

// 备份数据库导出sql语句
$db->export();

```

# 模型
> 参考demo-model.php

# 文件处理
```php
//创建文件
file::create("./data/test.txt", "Hello world!");

//删除文件
file::delete("./data/test.txt");

//读取文件
file::read("./data/test.txt");

//读取文件
file::rename("./data/test.txt","./data/ok.txt");

//判断文件是否存在
if(file::has("./data/test.txt")){
	//do something...
}

//获取文件权限 返回15是可写入权限
file::permissions("./data/test.txt");

//下载文件
file::download("./data/.txt");
file::download("./data/.txt","自定义内容下载");

//文件缓存(需要创建 ./data/cache 目录)
file::writeCache("test","内容");
file::readCache("test");


//获取文件后缀
file::ext("test.jpg"); //jpg


//获取CVS文件(返回数组)
file::csv("./.csv");

//缩放图片
file::thumbnail("./test.jpg", 100, 100); //按比例缩放图片

file::thumbnail("./test.jpg", 100, 100,"./target.jpg",true); //按大小裁剪图片
```

# 发送e-mail
```php
gum::mail([
	"to"       => "129@jinzhe.net",
	"subject"  => "test",
	"body"     => "test",
	"from"     => SMTP_MAIL,
	"server"   => SMTP_SERVER,
	"port"     => SMTP_PORT,
	"user"     => SMTP_USER,
	"password" => SMTP_PASSWORD,
]);
```


# 表单验证
> 参考demo-validate.php

# 格式化
- 金额
```php
echo format::rmb(12345);
```
- 容量
```php
echo format::size(1024);
```
- 时间
```php
echo format::ago($_SERVER['REQUEST_TIME']);
```
- 拼音
```php
echo format::pinyin('中国');
```

 