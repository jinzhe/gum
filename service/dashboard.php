<?php
class dashboard {
    // 依赖文件
    public static function depend() {
        return [
            "user",
        ];
    }
    public static function init() {
        new dashboard();
    }

    public function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }
    public function version() {
        gum::json(["code" => 200, "version" => VERSION]);
    }

    public function noop() {
        user::check($this->db);
        gum::json(["code" => 200]);
    }

    public function info() {
        user::check($this->db);
        gum::json([
            "code"   => 200,
            "result" => [
                "time"                => date("Y-m-d h:i:s", time()),
                "language"            => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                "os"                  => @PHP_OS,
                "php_version"         => @PHP_VERSION,
                "document_root"       => $_SERVER['DOCUMENT_ROOT'],
                "domain"              => $_SERVER['SERVER_NAME'],
                "software"            => $_SERVER['SERVER_SOFTWARE'],
                "run_mode"            => @php_sapi_name(),
                "disable_functions"   => @ini_get('disable_functions'),
                "post_max_size"       => @ini_get('post_max_size'),
                "upload_max_filesize" => @ini_get('upload_max_filesize'),
                "max_execution_time"  => @ini_get('max_execution_time') . 's',
                "memory_usage"        => @memory_get_usage(),
                "disk_free_space"     => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
                "mysql_version"       => $this->db->row("SELECT version() as version")["version"],
                "extensions"          => get_loaded_extensions(),
                "theme_dir"           => file_get_contents(ROOT . "theme/default.txt"),
                "theme_dirs"          => file::ls(ROOT . "theme", ["dir" => true]),
                "version"             => VERSION,
            ],
        ]);
    }

    public function theme() {
        user::check($this->db, ["level" => 255]);
        $dir = gum::query("dir");
        // 检测是否存在目录
        if (!in_array($dir, file::ls(ROOT . "theme", ["dir" => true]))) {
            gum::json([
                "code" => 500,
                "info" => "非法参数",
            ]);
        }
        $theme_dir = file_get_contents("theme/default.txt");
        if (!empty($theme_dir)) {
            $theme_config = include_once "theme/" . $theme_dir . "/config.php";
            foreach ($theme_config["config"] as $key => $value) {
                $data = [
                    "type"        => $value["type"],
                    "key"         => $key,
                    "value"       => $value["value"],
                    "description" => $value["description"],
                    "status"      => 1,
                ];
                $this->db->insert("config", $data);
            }
            file_put_contents(ROOT . "theme/default.txt", $dir);
        }

        gum::json([
            "code" => 200,
        ]);
    }
    // 清理upload附件
    public function clear() {
        user::check($this->db, ["level" => 255]);
        $rows = $this->db->rows("SELECT `path` FROM upload where bind_id='0' OR bind_id=''");
        if (count($rows) == 0) {
            gum::json(["code" => 400, "info" => "不需要清理"]);
        }
        foreach ($rows as $row) {
            @unlink(ROOT . $row["path"]);
        }
        $this->db->delete("upload", "bind_id='0' OR bind_id=''");
        gum::json(["code" => 200]);
    }
}