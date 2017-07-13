# 概览
> gum是基于php的开箱即用代码库。


# 数据库
```php
// 链接数据库
$db = new db("table","password","root","127.0.0.1",3306);

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
$db->delete("table",[
	"title" => "title",
	"content" => "content",
],"id=1");

// 清空表数据
$db->clear(["table1","table2"]);

// 备份数据库导出sql语句
$db->export();

```

# 模型
> 参考demo-model.php

# 文件处理
	## 缩放图片
	## 图片加水印
	## 上传文件
	## 下载文件
	## 创建／读取／删除／重命名
	## 读取文件权限
	## 缓存

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

# 输出json
```php
gum::json([
 "success"	=>	true,
]);
```

# 表单验证
```php
echo json_encode(check::validate([
	[
		"name"     => "title",
		"required" => true,
		"max"      => 100,
		"min"      => 5,
		"tips"     => [
			"required" => "标题不能为空",
			"max"      => "标题不能大于{{max}}",
			"min"      => "标题不能小于{{min}}",
		],
	],
	[
		"name"     => "content",
		"required" => true,
		"max"      => 50000,
		"min"      => 20,
		"tips"     => [
			"required" => "内容不能为空",
			"max"      => "内容不能大于{{max}}",
			"min"      => "内容不能小于{{min}}",
		],
	],
	[
		"name"     => "email",
		"required" => true,
		"max"      => 256,
		"type"     => "email",
		"tips"     => [
			"required" => "邮箱不能为空",
			"email"    => "邮箱地址不合法",
		],
	],
	[
		"name"     => "status",
		"required" => true,
		"values"   => [0, 1],
		"tips"     => [
			"required" => "状态不能为空",
			"values"   => "提交的参数不合法",
		],
	],
], [
	"title"   => "",
	"content" => "123",
	"email"   => "129@jinzhe.net",
	"status"  => 3,
]));
```

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
