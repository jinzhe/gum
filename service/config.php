<?php
/*
 * 设置服务
 *
 * 说明：自定义取数据
 */

class config {
    // 依赖文件
    public static function depend() {
        return [
            "user",
        ];
    }

    public static function install() {
        return [
            "CREATE TABLE `config` (
            `key` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
            `value` text CHARACTER SET utf8 NOT NULL,
            `description` varchar(100) NOT NULL DEFAULT '',
            `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (`key`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",
        ];
    }

    public static function init() {
        new config();
    }

    public function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }

    public function get() {
        $name   = gum::query("name");
        $result = $this->db->row("SELECT value FROM config WHERE name=?", ["params" => [$name]]);
        if ($result == false) {
            gum::json(["code" => 500]);
        }
        gum::json([
            "code"   => 200,
            "result" => $result["value"],
        ]);
    }

    public function gets() {
        $result = $this->db->rows("SELECT * FROM config");
        if ($result == false) {
            gum::json(["code" => 500]);
        }
        gum::json([
            "code"   => 200,
            "result" => $result,
        ]);
    }

    public static function value($db, $name) {
        $result = $db->row("SELECT value FROM config WHERE name=?", ["params" => [$name]]);
        if ($result == false) {
            return false;
        }
        return $result["content"];
    }

    public function save() {
        user::check($this->db, ["level" => 255]);
        $key         = gum::query("key");
        $value       = gum::query("value");
        $description = gum::query("description");
        $status      = gum::query("status");
        if ($key == "" || $value == "") {
            gum::json(["code" => 400]);
        }
        $action = false;
        $data   = [
            "key"         => $key,
            "value"       => $value,
            "description" => $description,
            "status"      => $status,
        ];
        $result = $this->db->row("SELECT value FROM config WHERE `key`=?", ["params" => [$key]]);
        if ($result == false) {
            $action = $this->db->insert("config", $data);
        } else {
            $action = $this->db->update("config", $data, "`key`='$key'");
        }
        if ($action) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }

    // 删除
    public function delete() {
        user::check($this->db, ["level" => 255]);
        $key = gum::query("key");
        if ($key == "") {
            gum::json(["code" => 400]);
        }
        $success = $this->db->delete("config", "`key`='$key'");
        if ($success) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }
}
