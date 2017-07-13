<?php
class file {
	/**
	 * 生成水印
	 *
	 * @param	string	$groundImage	要水印图像
	 * @param	string	$waterImage		水印图片
	 * @param	integer	$waterPos		水印位置
	 * @param	integer	$xOffset		X偏移
	 * @param	integer	$yOffset		Y偏移
	 * @return	void
	 */
	public static function watermark($groundImage, $waterImage = "", $waterPos = 0, $xOffset = 0, $yOffset = 0) {
		if (!empty($waterImage) && file_exists($waterImage)) {
			$water_info = getimagesize($waterImage);
			$water_w = $water_info[0]; //取得水印图片的宽
			$water_h = $water_info[1]; //取得水印图片的高
			switch ($water_info[2]) {
			//取得水印图片的格式
			case 1:$water_im = imagecreatefromgif($waterImage);
				break;
			case 2:$water_im = imagecreatefromjpeg($waterImage);
				break;
			case 3:$water_im = imagecreatefrompng($waterImage);
				break;
			}
		}
		//读取背景图片
		if (!empty($groundImage) && file_exists($groundImage)) {
			$ground_info = getimagesize($groundImage);
			$ground_w = $ground_info[0]; //取得背景图片的宽
			$ground_h = $ground_info[1]; //取得背景图片的高
			switch ($ground_info[2]) {
//取得背景图片的格式
			case 1:$ground_im = imagecreatefromgif($groundImage);
				break;
			case 2:$ground_im = imagecreatefromjpeg($groundImage);
				break;
			case 3:$ground_im = imagecreatefrompng($groundImage);
				break;
			}
		}
		$w = $water_w;
		$h = $water_h;
		//水印位置
		switch ($waterPos) {
		case 0:$posX = rand(0, ($ground_w - $w));
			$posY = rand(0, ($ground_h - $h));
			break; //随机
		case 1:$posX = 0;
			$posY = 0;
			break; //1为顶端居左
		case 2:$posX = ($ground_w - $w) / 2;
			$posY = 0;
			break; //2为顶端居中
		case 3:$posX = $ground_w - $w;
			$posY = 0;
			break; //3为顶端居右
		case 4:$posX = 0;
			$posY = ($ground_h - $h) / 2;
			break; //4为中部居左
		case 5:$posX = ($ground_w - $w) / 2;
			$posY = ($ground_h - $h) / 2;
			break; //5为中部居中
		case 6:$posX = $ground_w - $w;
			$posY = ($ground_h - $h) / 2;
			break; //6为中部居右
		case 7:$posX = 0;
			$posY = $ground_h - $h;
			break; //7为底端居左
		case 8:$posX = ($ground_w - $w) / 2;
			$posY = $ground_h - $h;
			break; //8为底端居中
		case 9:$posX = $ground_w - $w;
			$posY = $ground_h - $h;
			break; //9为底端居右
		default:$posX = rand(0, ($ground_w - $w));
			$posY = rand(0, ($ground_h - $h));
			break; //随机
		}
		//设定图像的混色模式
		imagealphablending($ground_im, true);
		imagecopy($ground_im, $water_im, $posX + $xOffset, $posY + $yOffset, 0, 0, $water_w, $water_h); //拷贝水印到目标文件
		@unlink($groundImage);
		switch ($ground_info[2]) {
		case 1:imagegif($ground_im, $groundImage);
			break;
		case 2:imagejpeg($ground_im, $groundImage, 100);
			break;
		case 3:imagepng($ground_im, $groundImage);
			break;
		}
		//释放内存
		if (isset($water_info)) {
			unset($water_info);
		}

		if (isset($water_im)) {
			imagedestroy($water_im);
		}

		unset($ground_info);
		imagedestroy($ground_im);
	}

	/**
	 * 生成缩略图
	 *
	 * @param	string	$image	临时文件对象
	 * @param	integer	$toW	生成宽度
	 * @param	integer	$toH	生成高度
	 * @param	string	$image_thumb	临时文件对象
	 * @param	bool	$mode	true:裁剪图片/false:按比例缩放图像算法
	 * @return	void
	 */
	public static function thumbnail($image, $toW, $toH, $image_thumb = "", $mode = false) {
		if ($image_thumb == "") {
#目标图像为空则替换原始图像
			$image_thumb = $image;
		}
		//获取原始图片大小
		$info = GetImageSize($image);
		// print_r($info);
		if ($info[2] == 1) {
			if (function_exists("imagecreatefromgif")) {
				$im = imagecreatefromgif($image);
			} else {
				return false;
			}
		} elseif ($info[2] == 2) {
			if (function_exists("imagecreatefromjpeg")) {
				$im = imagecreatefromjpeg($image);
			} else {
				return false;
			}
		} else {
			if (function_exists("imagecreatefrompng")) {
				$im = imagecreatefrompng($image);
			} else {
				return false;
			}

		}
		$srcW = ImageSX($im); //获取原始图片宽度
		$srcH = ImageSY($im); //获取原始图片高度

		$toWH = $toW / $toH; //获取缩图比例
		$srcWH = $srcW / $srcH; //获取原始图比例

		if (!$mode) {
			#按比例缩放图像算法
			if ($toWH <= $srcWH) {
				$ftoH = $toH;
				$ftoW = $ftoH * ($srcW / $srcH);
			} else {
				$ftoW = $toW;
				$ftoH = $ftoW * ($srcH / $srcW);
			}
			//创建画布并且复制原始图像到画布
			if (function_exists('imagecreatetruecolor') && (function_exists('imagecopyresampled'))) {
				$canvas = ImageCreateTrueColor($ftoW, $ftoH);
				#ImageCopyResized(dest,src,dx,dy,sx,sy,dw,dh,sw,sh);
				ImageCopyResampled($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH); #缩放粘帖
			} else {
				$canvas = ImageCreate($ftoW, $ftoH);
				ImageCopyResized($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
			}
		} else {
//裁剪
			if ($srcW > $srcH) {
				$ftoH = $toH;
				$ftoW = $ftoH * ($srcW / $srcH);
			} else {
				$ftoW = $toW;
				$ftoH = $ftoW * ($srcH / $srcW);
			}
			if (function_exists('imagecreatetruecolor') && (function_exists('imagecopyresampled'))) {
				$canvas = ImageCreateTrueColor($toW, $toH);
				ImageCopyResampled($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH); #缩放粘帖
			} else {
				$canvas = ImageCreate($toW, $toH);
				ImageCopyResized($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
			}
		}
		//输入图像
		if (strtolower(getExt($image)) == 'jpg') {
			imagejpeg($canvas, $image_thumb, 100);
		} elseif (strtolower(getExt($image)) == 'png') {
			imagepng($canvas, $image_thumb);
		} elseif (strtolower(getExt($image)) == 'gif') {
			imagegif($canvas, $image_thumb);
		}
		//回收资源
		ImageDestroy($canvas);
		ImageDestroy($im);
	}

	/**
	 * 读取文件或者文件夹的权限代码
	 *
	 * @param   string  $path    文件地址
	 * @return  int
	 */
	public static function permissions($path) {
		if (!file_exists($path)) {
			return false;
		}

		$mark = 0;
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			$test_file = $path . '/test.txt';
			if (is_dir($path)) {
				/* 检查目录是否可读 */
				$dir = @opendir($path);
				if ($dir === false) {
					return $mark;
				}
				//如果目录打开失败，直接返回目录不可修改、不可写、不可读
				if (@readdir($dir) !== false) {
					$mark ^= 1;
				}

				@closedir($dir); /* 检查目录是否可写 */
				$fp = @fopen($test_file, 'wb');
				if ($fp === false) {
					return $mark;
				}
				//如果目录中的文件创建失败，返回不可写。
				if (@fwrite($fp, 'directory access testing.') !== false) {
					$mark ^= 2;
				}
				//目录可写可读011，目录可写不可读 010
				@fclose($fp);
				@unlink($test_file);
				/* 检查目录是否可修改 */
				$fp = @fopen($test_file, 'ab+');
				if ($fp === false) {
					return $mark;
				}

				if (@fwrite($fp, "modify test.\r\n") !== false) {
					$mark ^= 4;
				}

				@fclose($fp);
				/* 检查目录下是否有执行rename()函数的权限 */
				if (@rename($test_file, $test_file) !== false) {
					$mark ^= 8;
				}

				@unlink($test_file);
			} elseif (is_file($path)) {
				/* 以读方式打开 */
				$fp = @fopen($path, 'rb');
				if ($fp) {
					$mark ^= 1;
				}
				//可读 001
				@fclose($fp); /* 试着修改文件 */
				$fp = @fopen($path, 'ab+');
				if ($fp && @fwrite($fp, '') !== false) {
					$mark ^= 6;
				}

				@fclose($fp); /* 检查目录下是否有执行rename()函数的权限 */
				if (@rename($path, $path . ".txt") !== false) {
					$mark ^= 8;
					@rename($path . ".txt", $path);
				}
			}
		} else {
			if (@is_readable($path)) {
				$mark ^= 1;
			}
			if (@is_writable($path)) {
				$mark ^= 14;
			}
		}
		return $mark;
	}
	// 是否存在
	public static function has($path) {
		return file_exists($path);
	}
	// 重命名
	public static function rename($from, $to) {
		rename($from, $to);
	}

	// 读取文件
	public static function read($path) {
		return file_get_contents($path);
	}

	// 创建文件夹或文件
	public static function create($path = './', $data = '') {
		if ($data == '') {
			if (!is_dir($path)) {
				self::create(dirname($path));
				if (!mkdir($path, 0777)) {
					return false;
				}
			}
		} else {
			file_put_contents($path, $data);
		}

	}

	// 删除文件夹或文件
	public static function delete($path) {
		if (is_dir($path)) {
			$handle = opendir($path);
			while ($file = readdir($handle)) {
				if ($file != "." && $file != "..") {
					if ($file) {
						unlink($file);
					} else {
						self::delete($file);
					}
				}
			}
			closedir($handle);
		} else {
			unlink($path);
		}
	}

	// 下载
	public static function download($filename, $data = '') {
		$ext = self::ext($filename);
		$name = date("YmdHis");
		if (file_exists($filename)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $name . '.' . $ext);
			header('Content-Length:' . filesize($filename));
			if ($data == '') {
				readfile($filename);
			} else {
				echo $data;
			}
			exit;
		} else {
			header("HTTP/1.1 404 Not Found");
		}
	}

	/**
	 * 上传文件
	 *
	 * @param	object	$upload	上传对象，可是单个或者数组
	 * @param	boolean	$target	上传目标
	 * @param	string	$ext	允许上传的文件后缀用逗号分隔
	 * @param	integer	$size	上传大小（单位M）
	 * @return	string
	 */
	public static function upload($options) {
		$upload = $options['upload'];
		if (isset($options['target'])) {
			$target = $options['target'];
		} else {
			$target = './';
		}
		if (isset($options['rename'])) {
			$rename = $options['rename'];
		} else {
			$rename = '';
		}

		if (isset($options['size'])) {
			$size = $options['size'];
		} else {
			$size = 5;
		}
		if (isset($options['exts'])) {
			$exts = $options['exts'];
		} else {
			$exts = 'jpg,jpeg,gif,png,bmp,torrent,zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,csv,mp3,wma,swf,flv,txt';
		}
		self::create($target);
		if (is_array($upload['name'])) {
			$return = array();
			foreach ($upload["name"] as $k => $v) {
				if (!empty($upload['name'][$k])) {
					$ext = self::ext($upload['name'][$k]);
					if (strpos($exts, $ext) !== false && $upload['size'][$k] < $size * 1024 * 1024) {
						$name = empty($rename) ? self::uploadName($ext) : self::uploadRename($rename, $ext);
						if (self::uploadMove($upload['tmp_name'][$k], $target . $name)) {

							$return[] = array(
								'name' => $name,
								'title' => $upload['name'][$k],
								'size' => $upload['size'][$k],
								'type' => $upload['type'][$k],
								'error' => $upload['error'][$k],
							);
						}
					}
				}
			}
			return $return;
		} else {
			$return = '';
			if (!empty($upload['name'])) {
				$ext = self::ext($upload['name']);
				if (strpos($exts, $ext) !== false && $upload['size'] < $size * 1024 * 1024) {
					$name = empty($rename) ? self::uploadName($ext) : self::uploadRename($rename, $ext);
					var_dump($target . $name);
					if (self::uploadMove($upload['tmp_name'], $target . $name)) {
						$return = array(
							'name' => $name,
							'title' => $upload['name'],
							'size' => $upload['size'],
							'type' => $upload['type'],
							'error' => $upload['error'],
						);
					}
				}
			}
		}
		return $return;
	}

	public static function uploadName($ext) {
		$name = date('YmdHis');
		for ($i = 0; $i < 3; $i++) {
			$name .= chr(mt_rand(97, 122));
		}
		$name = md5($name) . "." . $ext;
		return (string) $name;
	}

	public static function uploadRename($rename, $ext) {
		$name = $rename . "." . $ext;
		return (string) $name;
	}
	/**
	 * 移动上传文件
	 *
	 * @param	string	$from	文件来源
	 * @param	string	$target 移动目标地
	 * @return	boolean
	 */
	public static function uploadMove($from, $target = '') {
		if (function_exists("move_uploaded_file")) {
			if (move_uploaded_file($from, $target)) {
				@chmod($target, 0755);
				return true;
			}
		} elseif (copy($from, $target)) {
			@chmod($target, 0755);
			return true;
		}
		return false;
	}
	/**
	 * 检查上传文件
	 *
	 * @param	string	$name	临时文件
	 * @param	string	$ext 上传文件后缀
	 * @return	boolean
	 */
	private static function uploadCheck($name, $ext) {
		$str = $format = '';
		$file = @fopen($name, 'rb');
		if ($file) {
			$str = @fread($file, 0x400);
			@fclose($file);
			if (strlen($str) >= 2) {
				if (substr($str, 0, 4) == 'MThd' && $ext != 'txt') {
					$format = 'mid';
				} elseif (substr($str, 0, 4) == 'RIFF' && $ext == 'wav') {
					$format = 'wav';
				} elseif (substr($str, 0, 3) == "\xFF\xD8\xFF") {
					$format = 'jpg';
				} elseif (substr($str, 0, 4) == 'GIF8' && $ext != 'txt') {
					$format = 'gif';
				} elseif (substr($str, 0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
					$format = 'png';
				} elseif (substr($str, 0, 2) == 'BM' && $ext != 'txt') {
					$format = 'bmp';
				} elseif ((substr($str, 0, 3) == 'CWS' || substr($str, 0, 3) == 'FWS') && $ext != 'txt') {
					$format = 'swf';
				} elseif (substr($str, 0, 4) == "\xD0\xCF\x11\xE0") {
					// D0CF11E==DOCFILE==Microsoft Office Document
					if (substr($str, 0x200, 4) == "\xEC\xA5\xC1\x00" || $ext == 'doc') {
						$format = 'doc';
					} elseif (substr($str, 0x200, 2) == "\x09\x08" || $ext == 'xls') {
						$format = 'xls';
					} elseif (substr($str, 0x200, 4) == "\xFD\xFF\xFF\xFF" || $ext == 'ppt') {
						$format = 'ppt';
					}
				} elseif (substr($str, 0, 2) == "7z") {
					$format = '7z';
				} elseif (substr($str, 0, 4) == "PK\x03\x04") {
					$format = 'zip';
				} elseif (substr($str, 0, 4) == 'Rar!' && $ext != 'txt') {
					$format = 'rar';
				} elseif (substr($str, 0, 4) == "\x25PDF") {
					$format = 'pdf';
				} elseif (substr($str, 0, 3) == "\x30\x82\x0A") {
					$format = 'cert';
				} elseif (substr($str, 0, 4) == 'ITSF' && $ext != 'txt') {
					$format = 'chm';
				} elseif (substr($str, 0, 4) == "\x2ERMF") {
					$format = 'rm';
				} elseif ($ext == 'sql') {
					$format = 'sql';
				} elseif ($ext == 'txt') {
					$format = 'txt';
				} elseif ($ext == 'htm') {
					$format = 'htm';
				} elseif ($ext == 'html') {
					$format = 'html';
				} elseif (substr($str, 0, 3) == 'FLV') {
					$format = 'flv';
				} else {
					$format = $ext;
				}
			}
		}
		return $format;
	}
	/**
	 * 获取文件后缀名
	 *
	 * @param	string $filename 文件名
	 * @return	string
	 */
	public static function ext($filename) {
		if (!empty($filename)) {
			$explode = explode(".", strtolower($filename));
			return end($explode);
		}
	}

	// 获取CVS文件
	function getCsv($handle) {
		$out = array();
		$n = 0;
		while ($data = fgetcsv($handle, 10000)) {
			$num = count($data);
			for ($i = 0; $i < $num; $i++) {
				$out[$n][$i] = $data[$i];
			}
			$n++;
		}
		return $out;
	}

	/**
	 * 写结果缓存文件
	 *
	 * @param   string  $name    缓存索引
	 * @param   string  $value    缓存索引
	 * @return  array   $data
	 */
	public static function writeCache($name, $value) {
		$path = '../data/cache/' . md5($name) . '.php';
		if ($value != '') {
			self::create($path, "<?php return " . var_export($value, true) . ";", LOCK_EX);
		}
	}

	/**
	 * 读结果缓存文件
	 * @param   string  $name    缓存索引
	 * @return  array   $data
	 */
	public static function readCache($name) {
		$path = '../data/cache/' . md5($name) . '.php';
		if (file_exists($path)) {
			return include $path;
		} else {
			return false;
		}
	}
}