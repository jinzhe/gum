<?php
define('VERSION', 'v0.0.4');

require "config.php";
require "server.php";
require "check.php";
require "format.php";
require "file.php";
require "net.php";
require "db.php";
require "model.php";
// 入口类
class gum {

	public static function init($options = array()) {
		// php 5.3以上版本
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			die('PHP version must be higher then v5.3.0.');
		}
		// 容错
		error_reporting(DEBUG ? E_ALL : 0);

		// 设置时间区域
		date_default_timezone_set(TIMEZONE);

		// SESSION开启
		if (isset($options['session'])) {
			@session_start();
			if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
				@ini_set('session.lazy_write', 0);
				@ini_set('session.use_trans_sid', 1);
			}
		}

		header("X-Powered-By:Gum " . VERSION);

		if (isset($options['headers'])) {
			foreach ($options['headers'] as $item) {
				header($item);
			}
		} else {
			header("content-type: text/html;charset=utf-8");
		}

		// 过滤请求参数
		if (count($_REQUEST) > 0) {
			foreach ($_REQUEST as $key => $value) {
				$_REQUEST[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
			}
		}
	}

	/**
	 * 获取参数
	 * @param   string  $name    get或者post
	 * @return  string
	 */
	public static function query($name = "", $default = false) {
		if ($name == "") {
			return $_SERVER["QUERY_STRING"];
		}
		$_REQUEST = array_merge($_GET, $_POST);
		// var_dump($_REQUEST);
		$name = trim($name);
		$action = isset($_REQUEST[$name]) ? $_REQUEST[$name] : ($default == false ? "" : $default);
		return $action;
	}

	/**
	 * 模板解析
	 * @param   string  $name    get或者post
	 * @return  string
	 */
	public static function template($content, $data) {
		// {{var}}
		// {{if var} ... {{elseif var}} ... {{else}}... {{/if}}
		// {{for items}} {{/for}}
		return $data;
	}

	/**
	 * 加密函数
	 * @param   string  $str    加密前的字符串
	 * @param   string  $key    密钥
	 * @return  string  加密后的字符串
	 */
	public static function encode($str, $key = KEY) {
		$tmp = '';
		$keylength = strlen($key);
		for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength) {
			$tmp .= substr($str, $i, $keylength) ^ $key;
		}
		return str_replace('=', '', base64_encode($tmp));
	}

	/**
	 * 解密函数
	 * @param   string  $str    加密后的字符串
	 * @param   string  $key    密钥
	 * @return  string  加密前的字符串
	 */
	public static function decode($str, $key = KEY) {
		$tmp = '';
		$keylength = strlen($key);
		$str = base64_decode($str);
		for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength) {
			$tmp .= substr($str, $i, $keylength) ^ $key;
		}
		return $tmp;
	}

	/**
	 * 截取字符串
	 *
	 * @param	string	$string		要截取的字符串
	 * @param	integer	$length		要截取的字数
	 * @param	boolean	$append		是否打印省略号移
	 * @return	string
	 */
	public static function cut($string, $length, $append = true) {
		$string = trim($string);
		$strlength = strlen($string);
		if ($length == 0 || $length >= $strlength) {
			return $string;
		} elseif ($length < 0) {
			$length = $strlength + $length;
			if ($length < 0) {
				$length = $strlength;
			}
		}
		if (function_exists('mb_substr')) {
			$newstr = mb_substr($string, 0, $length, "UTF-8");
		} elseif (function_exists('iconv_substr')) {
			$newstr = iconv_substr($string, 0, $length, "UTF-8");
		} else {
			for ($i = 0; $i < $length; $i++) {
				$tempstring = substr($string, 0, 1);
				if (ord($tempstring) > 127) {
					$i++;
					if ($i < $length) {
						$newstring[] = substr($string, 0, 3);
						$string = substr($string, 3);
					}
				} else {
					$newstring[] = substr($string, 0, 1);
					$string = substr($string, 1);
				}
			}
			$newstr = join($newstring);
		}
		if ($append && $string != $newstr) {
			$newstr .= '...';
		}
		return $newstr;
	}

	/**
	 * 创建SQL IN语法
	 *
	 * @param array $item_list		数组
	 * @param string $field_name	字段
	 * @return	string
	 */
	public static function sqlIn($item_list, $field_name = '') {
		if (empty($item_list)) {
			return $field_name . " IN ('') ";
		} else {
			if (!is_array($item_list)) {
				$item_list = explode(',', $item_list);
			}
			$item_list = array_unique($item_list);
			$item_list_tmp = '';
			foreach ($item_list AS $item) {
				if ($item !== '') {
					$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
				}
			}
			if (empty($item_list_tmp)) {
				return $field_name . " IN ('') ";
			} else {
				return $field_name . ' IN (' . $item_list_tmp . ') ';
			}
		}
	}

	// 发送邮件
	/*
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
	*/
	public static function mail($options) {
		if (!isset($options['charset'])) {
			$options['charset'] = "utf-8";
		}
		if (!isset($options['auth'])) {
			$options['auth'] = 1;
		}
		$options['subject'] = '=?' . $options['charset'] . '?B?' . base64_encode($options['subject']) . '?=';
		$options['body'] = chunk_split(base64_encode(preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $options['body'])));

		$headers = "";
		$headers .= "MIME-Version:1.0\r\n";
		$headers .= "Content-type:text/html\r\n";
		$headers .= "Content-Transfer-Encoding: base64\r\n";
		$headers .= "From: " . $options['from'] . "\r\n";
		$headers .= "Date: " . date("r") . "\r\n";
		list($msec, $sec) = explode(" ", microtime());
		$headers .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $options['from'] . ">\r\n";

		if (!$fp = fsockopen($options['server'], $options['port'], $errno, $errstr, 30)) {
			return "CONNECT - Unable to connect to the SMTP server";
		}

		stream_set_blocking($fp, true);

		$message = fgets($fp, 512);
		if (substr($message, 0, 3) != '220') {
			return "CONNECT - " . $message;
		}

		fputs($fp, ($options['auth'] ? 'EHLO' : 'HELO') . " befen\r\n");
		$message = fgets($fp, 512);
		if (substr($message, 0, 3) != 220 && substr($message, 0, 3) != 250) {
			return "HELO/EHLO - " . $message;
		}

		while (1) {
			if (substr($message, 3, 1) != '-' || empty($message)) {
				break;
			}
			$message = fgets($fp, 512);
		}

		if ($options['auth']) {
			fputs($fp, "AUTH LOGIN\r\n");
			$message = fgets($fp, 512);
			if (substr($message, 0, 3) != 334) {
				return $message;
			}

			fputs($fp, base64_encode($options['user']) . "\r\n");
			$message = fgets($fp, 512);
			if (substr($message, 0, 3) != 334) {
				return "AUTH LOGIN - " . $message;
			}

			fputs($fp, base64_encode($options['password']) . "\r\n");
			$message = fgets($fp, 512);
			if (substr($message, 0, 3) != 235) {
				return "AUTH LOGIN - " . $message;
			}
		}

		fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $options['from']) . ">\r\n");
		$message = fgets($fp, 512);
		if (substr($message, 0, 3) != 250) {
			fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $options['from']) . ">\r\n");
			$message = fgets($fp, 512);
			if (substr($message, 0, 3) != 250) {
				return "MAIL FROM - " . $message;
			}
		}
		foreach (explode(',', $options['to']) as $touser) {
			$touser = trim($touser);
			if ($touser) {
				fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser) . ">\r\n");
				$message = fgets($fp, 512);
				if (substr($message, 0, 3) != 250) {
					fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser) . ">\r\n");
					$message = fgets($fp, 512);
					return "RCPT TO - " . $message;
				}
			}
		}
		fputs($fp, "DATA\r\n");
		$message = fgets($fp, 512);
		if (substr($message, 0, 3) != 354) {
			return "DATA - " . $message;
		}
		fputs($fp, $headers);
		fputs($fp, "To: " . $options['to'] . "\r\n");
		fputs($fp, "Subject: " . $options['subject'] . "\r\n");
		fputs($fp, "\r\n\r\n");
		fputs($fp, $options['body'] . "\r\n.\r\n");
		$message = fgets($fp, 512);
		if (substr($message, 0, 3) != 250) {
			return "END - " . $message;
		}
		fputs($fp, "QUIT\r\n");
		return true;
	}

	public static function randomCode($length = 6) {
		echo substr(str_shuffle("012345678901234567890123456789"), 0, $length);
	}

	public static function uuid($prefix = "") {
		$str = md5(uniqid(mt_rand(), true));
		$uuid = substr($str, 0, 8) . '-';
		$uuid .= substr($str, 8, 4) . '-';
		$uuid .= substr($str, 12, 4) . '-';
		$uuid .= substr($str, 16, 4) . '-';
		$uuid .= substr($str, 20, 12);
		return $prefix . $uuid;
	}

	public static function json($data, $mode = JSON_NUMERIC_CHECK) {
		header("content-type: application/json;charset=utf-8");
		echo json_encode($data, $mode);
		die();
	}
}