<?php
/*
 * 设置服务
 *
 * 说明：自定义取数据
 */

class config {
    // 依赖文件
    static function depend() {
        return [
            "user","upload"
        ];
    }

    static function install() {
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

    static function init() {
        new config();
    }

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }

 
    function gets() {
        $keys        = gum::query("keys");
        $sql="SELECT * FROM config WHERE status=1";
        if(!empty($keys)){
            $sql.=" AND `key` IN (".$keys.")";
        }
        $result = $this->db->rows($sql);
        if ($result == false) {
            $result=[];
        }
        gum::json([
            "code"   => 200,
            "result" => $result,
        ]);
    }

 
    static function get($db,$keys=[]) {
        $sql="SELECT `key`,`value` FROM config WHERE status=1";
        if(!empty($keys)){
            $sql.=" AND `key` IN (".implode(",",$keys).")";
        }
        $result = $db->rows($sql);
        if ($result == false) {
            return false;
        }
        return $result;
    }

    function save() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"config"
        ]);
        $key         = gum::query("key");
        $value       = gum::query("value");
        $description = gum::query("description");
        $status      = gum::query("status");
        $type      = gum::query("type");
        $ids         = gum::query("ids");
        if ($key == "") {
            gum::json(["code" => 400]);
        }
        if(preg_match("|^[a-zA-Z0-9-_\.]*$|U",$key)==0){
            gum::json(["code" => 401,"info"=>"变量不合法"]);
        }
        $action = false;
        $data   = [
            "key"         => $key,
            "value"       => $value,
            "description" => $description,
            "status"      => $status,
            "type"  =>    $type,
        ];
        $result = $this->db->row("SELECT value FROM config WHERE `key`=?", ["params" => [$key]]);
        if ($result == false) {
            $action = $this->db->insert("config", $data);
        } else {
            $action = $this->db->update("config", $data, "`key`='$key'");
            upload::remove_bind($this->db, "config", $key);
        }
        upload::bind($this->db, "config", $key,$ids);
        if ($action) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }

    // 删除
    function delete() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"config"
        ]);
        $key = gum::query("key");
        if ($key == "") {
            gum::json(["code" => 400]);
        }
        $success = $this->db->delete("config", "`key`='$key'");
        if ($success) {
            upload::remove_bind($this->db,"config",$key);
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }
}
