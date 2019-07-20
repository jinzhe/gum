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

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }

    function noop(){
        user::check($this->db);
        gum::json(["code" => 200]);
    }

    function info() {
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
            ],
        ]);
    }

    // 清理upload附件
    function clear() {
        user::check($this->db, ["level" => 255]);
        $rows = $this->db->rows("SELECT `path` FROM upload where bind_id='0'");
        if (count($rows) == 0) {
            gum::json(["code" => 400, "info" => "不需要清理"]);
        }
        foreach ($rows as $row) {
            @unlink(ROOT . $row["path"]);
        }
        $this->db->delete("upload", "bind_id=0");
        gum::json(["code" => 200]);
    }
}