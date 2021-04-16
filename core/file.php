<?php
// last update 20190419
class file {

    /**
     * 生成水印
     *
     * @param    string    $groundImage    要水印图像
     * @param    string    $waterImage        水印图片
     * @param    integer    $waterPos        水印位置
     * @param    integer    $xOffset        X偏移
     * @param    integer    $yOffset        Y偏移
     * @return    void
     */
    public static function watermark($groundImage, $waterImage = "", $waterPos = 0, $xOffset = 0, $yOffset = 0) {
        if (!empty($waterImage) && file_exists($waterImage)) {
            $water_info = getimagesize($waterImage);
            $water_w    = $water_info[0]; //取得水印图片的宽
            $water_h    = $water_info[1]; //取得水印图片的高
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
            $ground_w    = $ground_info[0]; //取得背景图片的宽
            $ground_h    = $ground_info[1]; //取得背景图片的高
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
            $posY        = rand(0, ($ground_h - $h));
            break; //随机
        case 1:$posX = 0;
            $posY        = 0;
            break; //1为顶端居左
        case 2:$posX = ($ground_w - $w) / 2;
            $posY        = 0;
            break; //2为顶端居中
        case 3:$posX = $ground_w - $w;
            $posY        = 0;
            break; //3为顶端居右
        case 4:$posX = 0;
            $posY        = ($ground_h - $h) / 2;
            break; //4为中部居左
        case 5:$posX = ($ground_w - $w) / 2;
            $posY        = ($ground_h - $h) / 2;
            break; //5为中部居中
        case 6:$posX = $ground_w - $w;
            $posY        = ($ground_h - $h) / 2;
            break; //6为中部居右
        case 7:$posX = 0;
            $posY        = $ground_h - $h;
            break; //7为底端居左
        case 8:$posX = ($ground_w - $w) / 2;
            $posY        = $ground_h - $h;
            break; //8为底端居中
        case 9:$posX = $ground_w - $w;
            $posY        = $ground_h - $h;
            break; //9为底端居右
        default:$posX = rand(0, ($ground_w - $w));
            $posY         = rand(0, ($ground_h - $h));
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
     * @param    string    $source    临时文件对象
     * @param    integer    $width    生成宽度
     * @param    integer    $height    生成高度
     * @param    string    $target    临时文件对象
     * @param    bool    $crop    true:裁剪图片否则按比例缩放图像算法
     * @return    void
     */
    public static function thumbnail($options) {
        // $image, $toW, $toH, $image_thumb = "", $mode = false
        if (!isset($options["source"])) {
            return false;
        }
        if (!isset($options["target"])) {
            $options["target"] = $options["source"];
        }
        if (!isset($options["width"])) {
            $options["width"] = 600;
        }
        if (!isset($options["height"])) {
            $options["height"] = 600;
        }

        //获取原始图片大小
        $info = getimagesize($options["source"]);
        if (!$info || ($info && $info[2] > 3)) {
            return false;
        }
        $srcW = $info[0]; //获取原始图片宽度
        $srcH = $info[1]; //获取原始图片高度
        if ($info[2] == 1) {
            //gif
            if (function_exists("imagecreatefromgif")) {
                $im = imagecreatefromgif($options["source"]);
            } else {
                return false;
            }
        } elseif ($info[2] == 2) {
            //jpg
            if (function_exists("imagecreatefromjpeg")) {
                $im = imagecreatefromjpeg($options["source"]);

                if (extension_loaded('exif')) {
                    // 处理ios 照片旋转
                    $exif = exif_read_data($options["source"]);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                        case 8:
                            $im   = imagerotate($im, 90, 0);
                            $srcW = $info[1];
                            $srcH = $info[0];
                            break;
                        case 3:
                            $im = imagerotate($im, 180, 0);
                            break;
                        case 6:
                            $im   = imagerotate($im, -90, 0);
                            $srcW = $info[1];
                            $srcH = $info[0];
                            break;
                        }
                    }
                }
            } else {
                return false;
            }
        } elseif ($info[2] == 3) {
            //png
            if (function_exists("imagecreatefrompng")) {
                $im = imagecreatefrompng($options["source"]);
            } else {
                return false;
            }
        } else {
            return false;
        }

        // 如果图片<=目标宽高则不需要缩小而直接压缩
        if ($srcW <= $options["width"]) {
            $ftoW = $srcW;
            $ftoH = $srcH;
            //创建画布并且复制原始图像到画布
            if (function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
                $canvas = imagecreatetruecolor($ftoW, $ftoH);
                imagecopyresampled($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH); #缩放粘帖
            } else {
                $canvas = imagecreate($ftoW, $ftoH);
                imagecopyresized($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
            }
        } else {
            if (!isset($options["crop"])) {
                #按比例缩放图像算法
                if ($srcW < $srcH) {
                    $ftoW = $options["height"] * ($srcW / $srcH);
                    $ftoH = $options["height"];
                } else {
                    $ftoW = $options["width"];
                    $ftoH = $options["width"] * ($srcH / $srcW);
                }
                //创建画布并且复制原始图像到画布
                if (function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
                    $canvas = imagecreatetruecolor($ftoW, $ftoH);
                    imagecopyresampled($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH); #缩放粘帖
                } else {
                    $canvas = imagecreate($ftoW, $ftoH);
                    imagecopyresized($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
                }
            } else {
                //裁剪
                if ($srcW > $srcH) {
                    $ftoH = $options["height"];
                    $ftoW = $ftoH * ($srcW / $srcH);
                } else {
                    $ftoW = $options["width"];
                    $ftoH = $ftoW * ($srcH / $srcW);
                }
                if (function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
                    $canvas = imagecreatetruecolor($options["width"], $options["height"]);
                    imagecopyresampled($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH); #缩放粘帖
                } else {
                    $canvas = imagecreate($options["width"], $options["height"]);
                    imagecopyresized($canvas, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
                }
            }
        }

        //生成图像
        if ($info[2] == 1) {
            imagegif($canvas, $options["target"]);
        } elseif ($info[2] == 2) {
            imageinterlace($canvas, 1);
            imagejpeg($canvas, $options["target"], isset($options["opacity"]) ? $options["opacity"] : 100);
        } elseif ($info[2] == 3) {
            imagepng($canvas, $options["target"]);
        }

        imagedestroy($canvas);
        imagedestroy($im);
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
    public static function create($path = './', $data = '', $flag = 0) {
        self::mkdirs(dirname($path));
        file_put_contents($path, $data, $flag);
    }

    // 创建目录
    public static function mkdirs($dir, $mode = 0777) {
        return is_dir($dir) or (self::mkdirs(dirname($dir)) and mkdir($dir, 0777) and @chmod($dir, $mode));
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
        $ext  = self::ext($filename);
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

    // 上传文件
    public static function upload($options = []) {
        $mimes = [
            "video/mp4"                                                                 => "mp4",
            "video/quicktime"                                                           => "mov",
            "video/x-ms-wmv"                                                            => "wmv",
            "video/webm"                                                                => "webm",
            "video/x-flv"                                                               => "flv",
            "image/photoshop"                                                           => "psd",
            "audio/mpeg"                                                                => "mp3",
            "image/x-icon"                                                              => "ico",
            "image/webp"                                                                => "webp",
            "image/jpeg"                                                                => "jpg",
            "image/png"                                                                 => "png",
            "image/gif"                                                                 => "gif",
            "image/bmp"                                                                 => "bmp",
            "application/pdf"                                                           => "pdf",
            "application/zip"                                                           => "zip",
            "application/x-rar-compressed"                                              => "rar",
            "application/x-rar"                                                         => "rar",
            "application/vnd.ms-powerpoint"                                             => "ppt",
            "application/vnd.ms-excel"                                                  => "xls",
            "application/msword"                                                        => "doc",
            "image/svg+xml"                                                             => "svg",
            "application/json"                                                          => "json",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document"   => "docx",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"         => "xlsx",
            "application/vnd.openxmlformats-officedocument.presentationml.presentation" => "pptx",
        ];

        $upload = $options['upload'];

        // print_r($upload);exit;

        $from = $upload['tmp_name'];
        $to   = $options['to'];

        // 没有$_FILES对象果断拒绝
        if (!isset($upload)) {
            return false;
        }
        // 判断文件是否是通过 HTTP POST 上传的
        if (!is_uploaded_file($from)) {
            return false;
        }
        // 处理上传
        if ($upload["error"] > 0) {
            return false;
        }

        // 验证mime类型白名单
        if (isset($options['mimes'])) {
            if (!isset($options['mimes'][$upload['type']])) {
                return false;
            }
        } else {
            if (!isset($mimes[$upload['type']])) {
                return false;
            }
        }

        // 如果MIME是常用图片,用getimagesize 检测是否是图片
        $info = getimagesize($from);
        if (in_array($upload['type'], ["image/jpeg", "image/gif", "image/png"]) && !$info) {
            return false;
        }
        // echo $to;
        // 创建文件夹
        self::mkdirs($to);

        // 创建文件名
        $name     = isset($options['name']) ? trim($options['name']) : gum::uuid();
        $ext      = $mimes[$upload['type']];
        $filename = $name . "." . $ext;

        // 如果是jpg和png格式直接重新生成，防止木马写入
        if (in_array($upload['type'], ["image/jpeg", "image/png"])) {

            if ($upload['type'] == "image/jpeg") {
                $img = imagecreatefromjpeg($from);
            } elseif ($upload['type'] == "image/png") {
                $img = imagecreatefrompng($from);
                imagesavealpha($img, true);
            }
            // 创建画布
            if (function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
                $canvas     = imagecreatetruecolor($info[0], $info[1]);
                $background = imagecolorallocatealpha($canvas, 0, 0, 0, 0);
                imagefill($canvas, 0, 0, $background);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                imagecopyresampled($canvas, $img, 0, 0, 0, 0, $info[0], $info[1], $info[0], $info[1]); #缩放粘帖

            } else {
                $canvas = imagecreate($info[0], $info[1]);
                imagecopyresized($canvas, $img, 0, 0, 0, 0, $info[0], $info[1], $info[0], $info[1]);
            }
            if ($upload['type'] == "image/jpeg") {
                imagejpeg($canvas, $to . $filename, 100);
            } elseif ($upload['type'] == "image/png") {
                imagepng($canvas, $to . $filename);
            }
            imagedestroy($img);
            imagedestroy($canvas);
            return $filename;
        } else {
            // 移动文件到目标目录
            if (move_uploaded_file($from, $to . $filename)) {
                @chmod($to . $filename, 0755);
                return $filename;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取文件后缀名
     *
     * @param    string $filename 文件名
     * @return    string
     */
    public static function ext($fileName) {
        if (!empty($fileName)) {
            $explode = explode(".", strtolower($fileName));
            return end($explode);
        }
    }
    /**
     * 导出excel(csv)
     *
     * @param   array  $data    缓存索引
     * @param   array  $headers    缓存索引
     * @param  string   $fileName
     */
    public static function export_csv($fileName, $data = array(), $headers = array()) {

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        //输出Excel列名信息
        foreach ($headers as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headers[$key] = $value;
        }
        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headers);
        //计数器
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {

            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }

            $row = $data[$i];
            foreach ($row as $key => $value) {
                $row[$key] = $value;
            }
            fputcsv($fp, $row);
        }
    }
    /**
     * 写结果缓存文件
     *
     * @param   string  $name    缓存索引
     * @param   string  $value    缓存索引
     * @return  array   $data
     */
    public static function write_cache($name, $value) {
        if ($value != '') {
            self::mkdirs(ROOT . '/cache');
            self::create('../cache/' . $name . '.php', "<?php return " . var_export($value, true) . ";", LOCK_EX);
        }
    }

    /**
     * 读结果缓存文件
     * @param   string  $name    缓存索引
     * @return  array   $data
     */
    public static function read_cache($name) {
        if (file_exists('../cache/' . $name . '.php')) {
            return include $path;
        } else {
            return false;
        }
    }

    public static function image_main_color($image) {
        $rTotal = $gTotal = $bTotal = $total = 0;
        $i      = imagecreatefromjpeg($image);
        for ($x = 0; $x < imagesx($i); $x++) {
            for ($y = 0; $y < imagesy($i); $y++) {
                $rgb = imagecolorat($i, $x, $y);
                $rTotal += ($rgb >> 16) & 0xFF;
                $gTotal += ($rgb >> 8) & 0xFF;
                $bTotal += $rgb & 0xFF;
                $total++;
            }
        }

        return array(
            'r' => round($rTotal / $total),
            'g' => round($gTotal / $total),
            'b' => round($bTotal / $total),
        );
    }

    // 获取列表
    public static function ls($dir, $options = []) {
        $dir  = rtrim($dir, "/");
        $temp = [];
        if (false != ($handle = opendir($dir))) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (isset($options["dir"])) {
                        if (is_dir($dir . "/" . $file)) {
                            array_push($temp, $file);
                        }
                    } elseif (isset($options["file"])) {
                        if (is_file($dir . "/" . $file)) {
                            array_push($temp, $file);
                        }
                    } else {
                        array_push($temp, $file);
                    }
                }
            }
            closedir($handle);
        }
        return $temp;
    }
}