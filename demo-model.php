<?php
require "./core/gum.php";

gum::init([
	"headers" => [
		"Access-Control-Allow-Origin: *",
	],
	"session" => true,
]);

$db = new db("mysql", "homepage");

$model = new model($db, [
	"table" => "db_post",
	"fields" => [
		"id" => [
			"type" => "bigint",
			"size" => 20,
			"primary" => true,
			"unsigned" => true,
		],
		"title" => [
			"type" => "varchar",
			"size" => 500,
			"validate" => [
				"name" => "title",
				"required" => true,
				"max" => 100,
				"min" => 5,
				"tips" => [
					"required" => "标题不能为空",
					"max" => "标题不能大于{{max}}",
					"min" => "标题不能小于{{min}}",
				],
			],
		],
		"content" => [
			"type" => "text",
			"validate" => [
				"name" => "content",
				"required" => true,
				"tips" => [
					"required" => "内容不能为空",
				],
			],
		],
		"time" => [
			"type" => "int",
			"size" => 10,
			"unsigned" => true,
			"default" => time(),

		],
		"status" => [
			"type" => "tinyint",
			"size" => 1,
			"unsigned" => true,
			"default" => 1,
		],
	],
]);

$action = gum::query("action");

if ($action == "list") {
	// 获取总数
	$count = $db->count("SELECT id FROM " . $model->table . " WHERE status=1");
	if ($count > 0) {
		$page = gum::query("page", 1);
		$pagesize = 10;
		$pagecount = ceil($count / $pagesize);
		$page = $page > $pagecount ? $pagecount : $page;
		$page = $page < 1 ? 1 : $page;
		// 获取ids
		$sql = "SELECT id FROM " . $model->table . " WHERE status=1 ORDER BY id DESC  LIMIT " . ((intval($page) - 1) * $pagesize) . "," . $pagesize;
		$rows = $db->rows($sql);
		if (count($rows) > 0) {
			$ids = [];
			foreach ($rows as $key => $value) {
				$ids[] = $value["id"];
			}
			$sql = "SELECT id,title FROM " . $model->table . " WHERE id IN (" . implode(",", $ids) . ") ORDER BY id DESC";
			$rows = $db->rows($sql);
			$result = [
				"success" => true,
				"count" => $count,
				"page" => $page,
				"pagesize" => $pagesize,
				"pagecount" => $pagecount,
				"result" => $rows,
			];
			gum::json($result);
		}
	} else {
		gum::json(["success" => true, "result" => []]);
	}

}

// create
if ($action == "create") {
	// 解析模型
	$result = $model->parse();
	// 拦截验证
	if (count($result["validates"]) > 0) {
		gum::json($validates);
	}
	$status = $db->insert($model->table, $result["items"]);
	gum::json($status ? ["success" => true, "id" => $db->id()] : ["success" => false]);
}

// update
if ($action == "update") {
	$id = gum::query("id");
	if (empty($id)) {
		gum::json(["success" => false]);
	}
	// 解析模型
	$result = $model->parse();
	// 拦截验证
	if (count($result["validates"]) > 0) {
		gum::json($validates);
	}
	$status = $db->update($model->table, $result["items"], "id=" . $id);
	gum::json(["success" => $status ? true : false]);
}

// delete
if ($action == "delete") {
	$id = gum::query("id");
	if (empty($id)) {
		gum::json(["success" => false]);
	}
	$status = $db->delete($model->table, "id=" . $id);
	gum::json(["success" => $status ? true : false]);
}