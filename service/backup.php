<?php
class backup {
    // 依赖文件
    static function depend() {
        return [
            "user",
        ];
    }
    static function init() {
        new backup();
    }

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }

    // 已备份DB列表
    function search() {
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