<?php
class model {
	private $db;
	private $model;
	public $table;

	function __construct($db, $model) {
		$this->db    = $db;
		$this->model = $model;
		$this->table = $model["table"];
		$this->create();
	}

	// 创建数据库
	function create() {
		$sql   = "CREATE TABLE IF NOT EXISTS " . $this->table . " (";
		$items = [];

		// 字段
		foreach ($this->model["fields"] as $key => $value) {

			$default = "DEFAULT ''";
			if (in_array($value["type"], ["int", "bigint", "smallint", "tinyint"])) {
				$default = "DEFAULT '0'";
			}
			$unsigned = "";
			if (isset($value["unsigned"])) {
				$unsigned = "unsigned";
			}
			$auto = "";
			if (isset($value["primary"])) {
				$auto    = "AUTO_INCREMENT";
				$default = "";
			}
			$size = "";
			if (isset($value["size"])) {
				$size = "(" . $value["size"] . ")";
			}
			if ($value["type"] == "text") {
				$default = "";
			}
			$items[] = "`" . $key . "` " . $value["type"] . $size . " " . $unsigned . " NOT NULL " . $default . " " . $auto;
		}

		// 追加索引
		foreach ($this->model["fields"] as $key => $value) {
			if (isset($value["primary"])) {
				$items[] = "PRIMARY KEY (" . $key . ")";
			} else if (isset($value["index"])) {
				$items[] = "INDEX KEY (" . $key . ")";
			} else if (isset($value["unique"])) {
				$items[] = "UNIQUE KEY (" . $key . ")";
			} else if (isset($value["fulltext"])) {
				$items[] = "FULLTEXT KEY (" . $key . ")";
			}
		}

		$sql .= implode(",", $items);
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		// echo $sql;
		// exit;
		$this->db->exec($sql);
	}

	function parse() {
		$items     = [];
		$validates = [];
		foreach ($this->model["fields"] as $key => $value) {
			if (!isset($value["primary"])) {
				if (empty(gum::query($key)) && isset($value["default"])) {
					$items[$key] = $value["default"];
				} else {
					$items[$key] = gum::query($key);
				}
			}
			if (isset($value["validate"])) {
				$validates[] = $value["validate"];
			}
		}
		if (count($items) > 0) {
			$validates = check::validate($data);
		}
		return [
			"items"     => $items,
			"validates" => $validates,
		];
	}

}