<?php
class server {
	// 获取当前路径
	public static function path($hasDomain = false) {
		$value = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		if ($hasDomain) {
			$value = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $value;
		}
		return $value;
	}

	//获取当前运行位置
	public static function base() {
		$php_self = self::path();
		$self = explode('/', $php_self);
		$self_count = count($self);
		$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
		if ($self_count > 1) {
			$url .= str_replace('/' . $self[$self_count - 1], '', $php_self);
		}
		if (substr($url, -1) != '/') {
			$url .= '/';
		}
		return $url;
	}

	// 获取ip
	public static function ip() {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (self::checkIP($ip)) {
			return $ip;
		} else {
			return '0.0.0.0';
		}
	}
	private static function checkIP($ip) {
		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			return false;
		} else {
			return true;
		}
	}

	// 获取操作系统
	public static function os($useragent = '') {
		if (empty($useragent)) {
			$useragent = $_SERVER["HTTP_USER_AGENT"];
		}
		if (strpos($useragent, "Windows NT 5.0")) {
			$os = "Windows 2000";
		} elseif (strpos($useragent, "Windows NT 5.1")) {
			$os = "Windows XP";
		} elseif (strpos($useragent, "Windows NT 5.2")) {
			$os = "Windows 2003";
		} elseif (strpos($useragent, "Windows NT 6.0")) {
			$os = "Windows Vista";
		} elseif (strpos($useragent, "Windows NT 6.1")) {
			$os = "Windows 7";
		} elseif (strpos($useragent, "Windows NT 6.2")) {
			$os = "Windows 8";
		} elseif (strpos($useragent, "Windows NT 6.3")) {
			$os = "Windows 10";
		} elseif (strpos($useragent, "Windows NT")) {
			$os = "Windows NT";
		} elseif (strpos($useragent, "Windows CE")) {
			$os = "Windows CE";
		} elseif (strpos($useragent, "ME")) {
			$os = "Windows ME";
		} elseif (strpos($useragent, "Windows 9")) {
			$os = "Windows 98";
		} elseif (strpos($useragent, "unix")) {
			$os = "Unix";
		} elseif (strpos($useragent, "linux")) {
			$os = "Linux";
		} elseif (strpos($useragent, "SunOS")) {
			$os = "SunOS";
		} elseif (strpos($useragent, "OpenBSD")) {
			$os = "OpenBSD";
		} elseif (strpos($useragent, "FreeBSD")) {
			$os = "FreeBSD";
		} elseif (strpos($useragent, "AIX")) {
			$os = "AIX";
		} elseif (strpos($useragent, "iPhone")) {
			$os = "iPhone";
		} elseif (strpos($useragent, "iPad")) {
			$os = "iPad";
		} elseif (strpos($useragent, "Mac")) {
			$os = "Mac";
		} elseif (strpos($useragent, "Android")) {
			$os = "Android";
		} else {
			$os = "Other";
		}

		return $os;
	}

	// 获取浏览器
	public static function bs($useragent = '') {
		if (empty($useragent)) {
			$useragent = $_SERVER["HTTP_USER_AGENT"];
		}
		if (strpos($useragent, "Opera")) {
			$browser = "Opera";
		} elseif (strpos($useragent, "Firefox")) {
			$browser = "Firefox";
		} elseif (strpos($useragent, "Chrome")) {
			$browser = "Chrome";
		} elseif (strpos($useragent, "MSIE 6")) {
			$browser = "IE6";
		} elseif (strpos($useragent, "MSIE 7")) {
			$browser = "IE7";
		} elseif (strpos($useragent, "MSIE 8")) {
			$browser = "IE8";
		} elseif (strpos($useragent, "MSIE 9")) {
			$browser = "IE9";
		} elseif (strpos($useragent, "MSIE 10")) {
			$browser = "IE10";
		} elseif (strpos($useragent, "MSIE 11")) {
			$browser = "IE11";
		} elseif (strpos($useragent, "Safari")) {
			$browser = "Safari";
		} else {
			$browser = "Other";
		}

		return $browser;
	}

	// 检测是否合法提交（在自己的服务器提交）
	public static function referer() {
		if (empty($_SERVER['HTTP_REFERER']) || (preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) != preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']))) {
			self::notFound();
		}
	}

	// 判断是否为机器人、爬虫
	public static function isSpider($useragent = '') {
		static $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
		static $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
		$useragent = empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent;
		if (!strpos($useragent, 'http://') && preg_match("/($kw_browsers)/i", $useragent)) {
			return false;
		} elseif (preg_match("/($kw_spiders)/i", $useragent)) {
			return true;
		} else {
			return false;
		}
	}

	// 判断是否为移动设备
	public static function isMobile() {
		if (preg_match("/iPhone|Android|phone|WAP|NetFront|JAVA|Opera\sMini|UCWEB|Windows\sCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|IEMobile|dopod|Nokia|samsung|PalmSource|Xda|PIEPlus|MEIZU|MIDP|CLDC/i", $_SERVER['HTTP_USER_AGENT'])) {
			return true;
		} else {
			return false;
		}
	}

	// 301永久重定向
	public static function redirect($url = './') {
		header('HTTP/1.1 301 Moved Permanently');
		Header("Location:$url");
		exit();
	}

	// 404
	public static function notFound($content = 'HTTP/1.1 404 Not Found') {
		header('HTTP/1.1 404 Not Found');
		exit($content);
	}
}