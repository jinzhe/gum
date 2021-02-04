<?php
/*
 * 评论服务
 *
 * 说明：用于击跳到相应网址
 * 相关表：comment
 */

class comment {
    // 依赖文件
    static function depend() {
        return [
            "user", "upload",
        ];
    }

    static function init() {
        new comment();
    }

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }

    function search() {
        $page_index = gum::query("page_index", [
            "default" => 1,
            "int"     => true,
        ]);
        $page_size = gum::query("page_size", [
            "default" => 20,
            "int"     => true,
        ]);
        $orderby   = gum::query("orderby", "sort");
        $sortby    = gum::query("sortby", "ASC");
        $status    = gum::query("status");
        $bind_type   = gum::query("bind_type");
        $bind_value = gum::query("bind_value");
        $user      = user::info($this->db);
        if ($user && $user["level"] == 255) {
            $sql = "SELECT * FROM comment WHERE 1=1";
            if ($status != "") {
                $sql .= " AND status=$status";
            }
        } else {
            $sql = "SELECT * FROM comment WHERE status=1";
        }

        if ($bind_type != "") {
            $sql .= " AND bind_type=$bind_type";
        }

        if ($bind_value != "") {
            $sql .= " AND bind_value=$bind_value";
        }
        $total = $this->db->count($sql); //先查数量
        $data  = [];
        if ($total > 0) {
            $page_index = max(min($page_index, ceil($total / $page_size)), 1);
            $rows       = $this->db->rows($sql . " ORDER BY $orderby $sortby LIMIT " . (($page_index - 1) * $page_size) . "," . $page_size); //获取ID(索引)
            $ids        = implode(array_column($rows, 'id'), ","); //取出id集合字符串

            $data = $this->db->rows("SELECT * FROM comment WHERE id IN (" . $ids . ") ORDER BY $orderby $sortby");
        }
        gum::json([
            "code"   => 200,
            "result" => [
                "total" => $total,
                "data"  => $data,
            ],
        ]);
    }

    // 发布
    function publish() {
        // user::check($this->db);
        $bind_type   = gum::query("bind_type");
        $bind_value = gum::query("bind_value");
        $token     = gum::query("token");
        $nickname  = gum::query("nickname");
        $email     = gum::query("email");
        $content   = gum::query("content");
        $parent_id = gum::query("parent_id", [
            "default" => 0,
            "int"     => true,
        ]);
        if ($content == "") {
            gum::json(["code" => 400, "info" => "评论内容不能为空"]);
        }
        // 限制同一个ip地址只能评论10次一天
        $zero        = strtotime(date('Y-m-d'));
        $five_minute = time() - 60 * 5;
        $ip          = gum::ip();
        $ip_count    = $this->db->count("SELECT * FROM comment WHERE ip='$ip' AND time>$zero");
        if ($ip_count > 10) {
            gum::json(["code" => 401, "info" => "今日发布评论次数已达到上限"]);
        }
        // 5分钟内同一个ip只能评论一次
        $ip_count = $this->db->count("SELECT * FROM comment WHERE ip='$ip' AND time>$five_minute");
        if ($ip_count > 0) {
            gum::json(["code" => 402, "info" => "5分钟内只能发布一次"]);
        }
        // 过滤危险评论
        $words = ["法轮功", "婊子", "妓女"];
        foreach ($words as $word) {
            if (strpos($content, $word) > 0) {
                break;
                gum::json(["code" => 403, "info" => "您发布的帖子含有禁用词语"]);
            }
        }

        $data              = [];
        $data["content"]   = $content;
        $data["reply"]     = "";
        $data["ip"]        = $ip;
        $data["time"]      = time();
        $data["status"]    = 1;
        $data["parent_id"] = 0;
        if ($token != "" && defined("STATUS_COMMENT_USE_USER") && STATUS_COMMENT_USE_USER) {
            $userinfo         = user::info($this->db);
            $data["user_id"]  = $userinfo["id"];
            $data["nickname"] = $userinfo["nickname"];
            $data["email"]    = $userinfo["email"];
        } else {
            $data["user_id"]  = 0;
            $data["nickname"] = $nickname;
            $data["email"]    = $email;
        }

        $data["bind_type"]   = $bind_type;
        $data["bind_value"] = $bind_value;
        $action            = $this->db->insert("comment", $data);
        // $id           = $this->db->id();

        if ($action) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }
    // 发布
    function reply() {
        user::check($this->db, ["level" => 255]);

        $id      = gum::query("id", ["default" => 0, "int" => true]);
        $content = gum::query("content");
        $reply   = gum::query("reply");
        $status  = gum::query("status", ["default" => 0, "int" => true]);
        if ($content == "" || $reply == "") {
            gum::json(["code" => 400, "info" => "评论内容不能为空"]);
        }
        $action          = false;
        $data            = [];
        $data["reply"]   = $reply;
        $data["content"] = $content;
        $data["status"]  = $status;
        $action          = $this->db->update("comment", $data, "id='$id'");

        if ($action) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }

    // 删除
    function delete() {
        user::check($this->db, ["level" => 255]);

        $id = gum::query("id");
        if ($id == "") {
            gum::json(["code" => 400]);
        }
        $success = $this->db->delete("comment", "id=" . $id);
        if ($success) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }

}