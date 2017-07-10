<?php
require "./core/gum.php";

gum::init([
	"headers" => [
		"Access-Control-Allow-Origin: *",
		"content-type: application/json;charset=utf-8",
	],
	"session" => true,
]);

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