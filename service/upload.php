<?php
/*
 * 上传服务
 *
 * 说明：上传图片、文件、移除文件
 * 相关表：upload
 */

class upload {
    const WHITELIST = [
        "user",
        "post",
        "link",
        "config",
    ];
    // 依赖文件
    public static function depend() {
        return [
            "user",
        ];
    }

    public static function init() {
        new upload();
    }

    public function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
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
    }
// 根据自定义json结构请求返回数据
    // {"bind_id":10,"bind_type":"post"}
    public function data() {
        $data = gum::query("data");
        if ($data == "") {
            gum::json(["code" => 500]);
        }
        $data = json_decode($data);
        if ($data == false) {
            gum::json(["code" => 500]);
        }
        $result = [];

        if (isset($data->bind_id) && isset($data->bind_type)) {
            $result["bind"] = $this->db->rows("SELECT id,path,name,type FROM upload WHERE bind_id='" . $data->bind_id . "' AND bind_type='" . $data->bind_type . "'");
            foreach ($result["bind"] as &$row) {
                $row["url"] = DOMAIN . "/" . $row["path"];
                if (in_array($row["type"], ["image/jpeg", "image/png", "image/gif"])) {
                    $row["image"] = true;
                } else {
                    $row["mage"] = false;
                }
                unset($row["path"]);
            }
        }
        gum::json(["code" => 200, "result" => $result]);
    }

    // 上传文件
    public function go() {
        user::check($this->db);
        @ini_set('memory_limit', '1024M');
        if (isset($_FILES["file"]["name"])) {
            $original  = gum::query("original"); //是否保留原图
            $thumbnail = gum::query("thumbnail"); //是否生成缩图，1200x1200,600x600
            $bind_type = gum::query("bind_type");

            // 验证绑定模块是否安全
            if (!in_array($bind_type, self::WHITELIST)) {
                gum::json([
                    "code" => 500,
                    "info" => "参数无效",
                ]);
            }

            // 验证上传文件错误
            switch ($_FILES["file"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
                $info = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $info = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $info = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $info = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $info = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $info = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $info = "File upload stopped by extension";
                break;
            }
            if ($_FILES["file"]["error"] > 0) {
                gum::json([
                    "code" => 500,
                    "info" => $info,
                ]);
            }

            // 判断上传的大小
            if ($_FILES["file"]['size'] > 50 * 1024 * 1024) {
                gum::json([
                    "code" => 501,
                    "info" => "上传文件超过50MB",
                ]);
            }

            $to   = "upload/" . $bind_type . "/" . date("Ym") . "/"; //相对路径
            $path = ROOT . $to; //物理路径
            if (file::permissions(ROOT . "upload") != 15) {
                gum::json([
                    "code" => 503,
                    "info" => "upload文件夹无法写入",
                ]);
            }
            if (is_dir(ROOT . "upload/" . $bind_type . "/") && file::permissions(ROOT . "upload/" . $bind_type . "/") != 15) {
                gum::json([
                    "code" => 503,
                    "info" => "upload/" . $bind_type . "/文件夹无法写入",
                ]);
            }
            if (is_dir($path) && file::permissions($path) != 15) {
                gum::json([
                    "code" => 503,
                    "info" => $to . "文件夹无法写入",
                ]);
            }
            // 上传文件
            $file = file::upload(["upload" => $_FILES["file"], "to" => $path]);

            if (is_string($file)) {
                $mimes = [
                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/gif"  => "gif",
                ];
                // 如果是图片且设置了缩图大小(多个小图)
                if ($thumbnail != "" && in_array($_FILES["file"]["type"], array_keys($mimes))) {
                    $thumbnail_size = explode(",", $thumbnail);
                    if (count($thumbnail_size) > 0) {
                        $find = $mimes[$_FILES["file"]["type"]];
                        foreach ($thumbnail_size as $item) {
                            $size = explode("x", $item);
                            file::thumbnail([
                                "source"  => $path . $file,
                                "target"  => $path . str_replace("." . $find, "_" . $item, $file) . ".jpg",
                                "width"   => (int)$size[0],
                                "height"  => (int)$size[1],
                                "opacity" => UPLOAD_IMAGE_OPACITY,
                            ]);
                        }
                    }

                }
                // 设置了原图压缩才去压缩原图（本质为了节省服务器硬盘空间）
                if ($_FILES["file"]["type"] == "image/jpeg" && $original != "") {
                    $original_size = explode("x", $original);
                    file::thumbnail([
                        "source"  => $path . $file,
                        "width"   => (int)$original_size[0],
                        "height"  => (int)$original_size[1],
                        "opacity" => UPLOAD_IMAGE_OPACITY,
                    ]);
                }
                $this->db->insert("upload", [
                    "path"      => $to . $file,
                    "name"      => $_FILES["file"]["name"],
                    "type"      => $_FILES["file"]["type"],
                    "size"      => $_FILES["file"]["size"],
                    "time"      => time(),
                    "bind_id"   => 0,
                    "bind_type" => $bind_type,
                ]);
                gum::json([
                    "code"   => 200,
                    "result" => [
                        "id"    => $this->db->id(),
                        "url"   => DOMAIN . "/" . $to . $file,
                        "name"  => $_FILES["file"]["name"],
                        "type"  => $_FILES["file"]["type"],
                        "image" => in_array($_FILES["file"]["type"], [
                            "image/jpeg",
                            "image/png",
                            "image/gif",
                        ]),
                    ],
                ]);
            } else {
                gum::json([
                    "code" => 500,
                    "info" => "文件上传失败，请检查文件写入权限",
                ]);
            }
        } else {
            gum::json([
                "code" => 404,
                "info" => "文件未上传",
            ]);
        }
    }

    // 绑定关系
    public static function bind($db, $bind_type, $bind_id, $ids) {
        $db->update("upload", [
            "bind_type" => $bind_type,
            "bind_id"   => $bind_id,
        ], "id IN ($ids)");
    }

    // 解除绑定
    public static function remove_bind($db, $bind_type, $bind_id, $ids = "") {
        if ($ids != "") {
            $where = "id IN ($ids)";
        } else {
            $where = "bind_id='$bind_id' AND bind_type='$bind_type'";
        }
        // $rows = $db->rows("SELECT * FROM upload WHERE $where");
        // foreach ($rows as $row) {
        //     file::delete(ROOT . $row["path"]); //删除物理文件
        // }
        $db->update("upload", "bind_id=0", $where);
    }

    // 删除文件
    public function delete() {
        user::check($this->db, ["level" => 255]);

        $id = gum::query("id");
        if ($id == "") {
            gum::json(["code" => 400]);
        }
        $row = $this->db->row("SELECT path FROM upload WHERE id=?", [
            "params" => [$id],
        ]);

        if (count($row) == 0) {
            gum::json(["code" => 500]);
        }

        file::delete(ROOT . $row["path"]);

        $success = $this->db->delete("upload", "id=?", [
            "params" => [$id],
        ]);
        if ($success) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }
}