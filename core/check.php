<?php
// 表单验证
class check {

	// 验证表单数据
	/*
		echo check::validate([
			[
				"name" => "title",
				"required" => true,
				"max" => 100,
				"min" => 5,
				"tips"=>[
					"required" =>"标题不能为空",
					"max"=>"标题不能大于{{max}}",
					"min"=>"标题不能小于{{min}}",
				]
			],
			[
				"name" => "content",
				"required" => true,
				"max" => 50000,
				"min" => 20,
				"tips"=>[
					"required" =>"内容不能为空",
					"max"=>"内容不能大于{{max}}",
					"min"=>"内容不能小于{{min}}",
				]
			],
			[
			 	"name" => "email",
				"required" => true,
				"max" => 256,
				"type" => "email",
				"tips"=>[
					"required" =>"邮箱不能为空",
					"email"=>"邮箱地址不合法",
				]
			],
			[
				"name" => "status",
				"required" => true,
				"values" =>[0,1],
				"tips"=>[
					"required" =>"状态不能为空",
					"values"=>"提交的参数不合法",
				]
			],
		],[
			"title"=>"",
			"content"=>"123",
			"email"=>"129@jinzhe.net",
			"status"=>3,
		]);
	*/
	public static function validate($items, $target = false) {
		// var_dump($target);
		if ($target == false) {
			$target = array_merge($_GET, $_POST);
		}
		$errors = [];
		// 替换参数
		function replace($tip, $item) {
			return preg_replace_callback("/\{\{(.*)\}\}/", function ($matches) use ($item) {
				return $item[$matches[1]];
			}, $tip);
		}
		if (count($items) > 0) {
			foreach ($items as $item) {
				$value = isset($target) ? $target[$item["name"]] : $_REQUEST[$item["name"]]; //获取数据
				$count = strlen(trim($value));
				// 验证是否有值
				if (isset($item["required"]) && $item["required"] == true) {
					if (empty($value)) {
						$tip = "required";
						if (isset($item["tips"]) && isset($item["tips"]["required"])) {
							$tip = $item["tips"]["required"];
							$tip = replace($tip, $item);
						}
						$errors[$item["name"]][] = $tip;
					}
				}

				// 验证邮箱
				if (isset($item["type"]) && $item["type"] == 'email') {
					if (!self::email($value)) {
						$tip = "email";
						if (isset($item["tips"]) && isset($item["tips"]["email"])) {
							$tip = $item["tips"]["email"];
							$tip = replace($tip, $item);
						}
						$errors[$item["name"]][] = $tip;
					}
				}

				// 验证最大字数
				if (isset($item["max"]) && $count > 0) {
					if ($count > $item["max"]) {
						$tip = "max";
						if (isset($item["tips"]) && isset($item["tips"]["max"])) {
							$tip = $item["tips"]["max"];
							$tip = replace($tip, $item);
						}
						$errors[$item["name"]][] = $tip;
					}
				}
				// 验证最大字数
				if (isset($item["min"]) && $count > 0) {
					if ($count < $item["min"]) {
						$tip = "min";
						if (isset($item["tips"]) && isset($item["tips"]["min"])) {
							$tip = $item["tips"]["min"];
							$tip = replace($tip, $item);
						}
						$errors[$item["name"]][] = $tip;
					}
				}

				// 包含指定值范围
				if (isset($item["values"])) {
					if (!in_array($value, $item["values"])) {
						$tip = "values";
						if (isset($item["tips"]) && isset($item["tips"]["values"])) {
							$tip = $item["tips"]["values"];
							$tip = replace($tip, $item);
						}
						$errors[$item["name"]][] = $tip;
					}
				}
			}
		}
		return $errors;
	}

	// 检测E-MAIL 合法性
	public static function email($email) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		} else {
			return true;
		}

	}

	// 检测网址
	public static function url($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			return false;
		} else {
			return true;
		}
	}
	// 中国身份证
	// 手机号码

	// 最否在指定日期内
	public static function inTime($time, $day = 3) {
		return $_SERVER['REQUEST_TIME'] - $time < 3600 * 24 * $day ? true : false;
	}

	// 检查XSS攻击
	public static function xss() {
		$temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
		if (strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
			self::notFound();
		}
	}

}