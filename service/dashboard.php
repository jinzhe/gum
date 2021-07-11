<?php
class dashboard {
    // 依赖文件
    static function depend() {
        return [
            "user",
        ];
    }
    static function init() {
        new dashboard();
    }

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }
    function version() {
        gum::json(["code" => 200, "version" => VERSION]);
    }

    function noop() {
        user::check($this->db);
        gum::json(["code" => 200]);
    }

    function info() {
        user::check($this->db);
        gum::json([
            "code"   => 200,
            "result" => [
                "time"                => date("Y-m-d H:i:s", time()),
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

    function theme() {
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
    function clear() {
        user::check($this->db, ["level" => 255]);
        if(file::has(ROOT."index.html")){
            unlink(ROOT . "index.html");
        }
        foreach (glob(ROOT."tag/*") as $file) {
            unlink($file);
        }
        foreach (glob(ROOT."post/*") as $file) {
            unlink($file);
        }
        $rows = $this->db->rows("SELECT `path` FROM upload where bind_id='0' OR bind_id=''");
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                unlink(ROOT . $row["path"]);
            }
            $this->db->delete("upload", "bind_id='0' OR bind_id=''");
        }
        gum::json(["code" => 200]);
    }

    function backup_list() {
        user::check($this->db, [
            "level" => 255,
        ]);
        $files = [];
        if ($handle = opendir(ROOT . "/backup/")) {
            $no = 0;
            while (false !== ($file = readdir($handle))) {
                if (strpos($file, '.sql') !== false) {
                    $files[$no]['name'] = "/backup/" . $file;
                    $files[$no]['time'] = date('Y-m-d H:i:s', filemtime(ROOT . "/backup/" . $file));
                    $no++;
                }
            }
            closedir($handle);
        }
        $time = [];
        foreach ($files as $k => $v) {
            $time[$k] = $v['time'];
        }
        array_multisort($time, SORT_DESC, SORT_STRING, $files); //按时间排序
        gum::json([
            "code"   => 200,
            "result" => $files,
        ]);
    }

    // 备份DB
    function backup() {
        user::check($this->db, [
            "level" => 255,
        ]);
        $content = $this->db->export();
        // echo $content;exit;
        $date = date('Ymd');
        $file = md5($date . mt_rand(0, 99999));
        $file = $date . "_" . substr($file, 0, 4) . ".sql";
        file_put_contents(ROOT . "/backup/" . $file, $content);
        gum::json([
            "code" => 200,
            "file" => "/backup/" . $file,
        ]);
    }

    // 恢复DB
    function restore() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"backup"
        ]);
        $file = urldecode(gum::query("file"));
        $file = ROOT . $file;
        if (file_exists($file)) {
            $content = file_get_contents($file);
            // echo $content;
            $sqls  = explode("\r\n\r\n\r\n", $content);
            $count = count($sqls);
            $total = 1;
            foreach ($sqls as $sql) {

                if (!empty(trim($sql))) {
                    $this->db->exec($sql);
                    $total++;
                }
            }
            // exit;
            if ($count == $total) {
                gum::json([
                    "code" => 200,
                ]);
            } else {
                gum::json([
                    "code" => 502,
                ]);
            }

        } else {
            gum::json([
                "code" => 500,
            ]);
        }

    }
}