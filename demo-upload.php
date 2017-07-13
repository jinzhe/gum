<?php
require "./core/gum.php";

gum::init([
	"headers" => [
		"Access-Control-Allow-Origin: *",
		"content-type: application/json;charset=utf-8",
	],
]);

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
	header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
	header('Access-Control-Max-Age:1728000');
	header('Content-Type:text/plain charset=UTF-8');
	header('Content-Length: 0', true);
	header('status: 204');
	header('HTTP/1.0 204 No Content');
	exit;
}

if (isset($_FILES["file"]["name"])) {
	echo json_encode(file::upload([
		"upload" => $_FILES["file"],
		"target" => "./data/",
	]));
	exit;
}

$chunked = gum::query("chunked");

if ($chunked == "true") {
	ini_set("memory_limit", "-1");
	$chunkedID = gum::query("chunkedID");
	$chunkedTotal = gum::query("chunkedTotal");
	$chunkedIndex = gum::query("chunkedIndex");
	$data = $_FILES['chunkedData'];

	// 读临时文件
	$handleRead = fopen($data["tmp_name"], "r+");
	$handleReadData = fread($handleRead, filesize($data["tmp_name"]));
	$handleWrite = fopen("./data/" . $chunkedID . "-" . $chunkedIndex . ".tmp", "w+");
	fwrite($handleWrite, $handleReadData);
	fclose($handleWrite);
	fclose($handleRead);

	if (file::has("./data/" . $chunkedID . ".tmp")) {
		$count = file::read("./data/" . $chunkedID . ".tmp");
		$count = intval($count) + 1;
		$chunkedTotal = intval($chunkedTotal);
		// 最后一次合并文件并且返回json
		if ($chunkedTotal == $count) {
			$content = "";
			for ($i = 0; $i < $chunkedTotal; $i++) {
				$content .= file::read("./data/" . $chunkedID . "-" . $i . ".tmp");
			}
			$chunkedName = file::uploadName("zip");
			file::create("./data/" . $chunkedName, $content);
			// 删除临时文件
			for ($i = 0; $i < $chunkedTotal; $i++) {
				file::delete("./data/" . $chunkedID . "-" . $i . ".tmp");
			}
			file::delete("./data/" . $chunkedID . ".tmp");
			echo $chunkedName;
			exit;
		}

		file::create("./data/" . $chunkedID . ".tmp", $count);
	} else {
		file::create("./data/" . $chunkedID . ".tmp", 1);
	}
	echo true;
}
