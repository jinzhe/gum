<?php
/*
 * 帖子服务
 *
 * 说明：帖子curd，列表，搜索，自定义取数据
 */
class post {
    // 依赖文件
    public static function depend() {
        return [
            "user", "tag", "upload",
        ];
    }

    // 初始化
    public static function init() {
        new post();
    }

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }

    // 获取数据列表
    function search() {
        $page_index = gum::query("page_index", [
            "default" => 1,
            "int"     => true,
        ]);
        $page_size = gum::query("page_size", [
            "default" => 20,
            "int"     => true,
        ]);
        $fields  = gum::query("fields");
        $begin   = gum::query("begin");
        $end     = gum::query("end");
        $status  = gum::query("status");
        $keyword = gum::query("keyword");
        $tag_id  = gum::query("tag_id");
        $orderby = gum::query("orderby", "id");
        $sortby  = gum::query("sortby", "DESC");
        $user    = user::info($this->db);
        if ($user && $user["level"] == 255) {
            $sql = "SELECT * FROM post WHERE 1=1";
            if ($status != "") {
                $sql .= " AND status=$status";
            }
        } else {
            $sql = "SELECT * FROM post WHERE status=1";
        }

        // 根据标签显示结果
        if ($tag_id != "") {
            $sql .= " AND tag_id=$tag_id";
        }
        // 关键字搜索
        if ($keyword != "") {
            $sql .= " AND (INSTR(title,'" . $keyword . "') OR INSTR(tag_name,'" . $keyword . "'))";
        }
        if ($begin != "") {
            $sql .= " AND time>=$begin";
        }
        if ($end != "") {
            $sql .= " AND time<=$end";
        }
        // echo $sql;exit;
        $total = $this->db->count($sql); //先查数量
        $data  = [];
        if ($total > 0) {
            $page_index  = max(min($page_index, ceil($total / $page_size)), 1);
            $rows        = $this->db->rows($sql . " ORDER BY $orderby $sortby LIMIT " . (($page_index - 1) * $page_size) . "," . $page_size); //获取ID(索引)
            $ids         = implode(array_column($rows, 'id'), ","); //取出id集合字符串
            $safe_fields = ['id', 'cover', 'title', 'author', 'keywords', 'description', 'view', 'time', 'best', 'tag_id', 'tag_name'];
            if ($user && $user["level"] == 255) {
                $safe_fields[] = 'status';
            }
            // 检查是否是安全的字段
            if ($fields != "") {
                foreach (explode(",", $fields) as $item) {
                    if (!in_array($item, $safe_fields)) {
                        gum::json(["code" => 500, "info" => "参数不合法"]);
                        break;
                    }
                }
            } else {
                $fields = implode(",", $safe_fields);
            }
            $data = $this->db->rows("SELECT " . $fields . " FROM post WHERE id IN (" . $ids . ") ORDER BY $orderby $sortby");
        }
        gum::json([
            "code"   => 200,
            "result" => [
                "total" => $total,
                "data"  => $data,
            ],
        ]);
    }

    // 根据自定义json结构请求返回数据
    // {"new":true,"hot":true,"ids":"1,2,3,4","tags":"1,2,3","limit":10}
    function data() {
        $data = gum::query("data");
        if ($data == "") {
            gum::json(["code" => 400]);
        }
        $data = json_decode($data);
        if ($data == false) {
            gum::json(["code" => 500]);
        }
        $result = [];
        if (isset($data->new)) {
            $result["new"] = $this->db->rows("SELECT id,title,time,view FROM post WHERE status=1 ORDER BY id DESC LIMIT $data->limit");
        }
        if (isset($data->hot)) {
            $result["hot"] = $this->db->rows("SELECT id,title,time,view FROM post WHERE status=1  ORDER BY view DESC LIMIT $data->limit");
        }
        if (isset($data->best)) {
            $result["best"] = $this->db->rows("SELECT id,title,time,view FROM post WHERE status=1  ORDER BY view DESC LIMIT $data->limit");
        }
        if (isset($data->ids)) {
            $result["ids"] = $this->db->rows("SELECT id,title,time,view FROM post WHERE status=1 AND id IN($data->ids)");
        }
        if (isset($data->tags)) {
            $result["tags"] = $this->db->rows("SELECT id,title,time,view FROM post AS a LEFT JOIN (SELECT bind_id FROM tag_bind WHERE id IN($data->tags) AND bind_type='post') AS b on a.id=b.bind_id WHERE b.bind_id is not null AND a.status=1 LIMIT $data->limit");
        }
        gum::json(["code" => 200, "result" => $result]);
    }

    // 一条帖子
    function get() {
        $id     = gum::query("id");
        $result = $this->db->row("SELECT * FROM post WHERE id=" . $id);
        if ($result == false) {
            gum::json(["code" => 500]);
        }
        $this->db->exec("UPDATE post SET view=view+1 WHERE id=" . $id); // 更新阅读数量
        gum::json(["code" => 200, "result" => $result]);
    }

    // 创建 & 更新 帖子
    function save() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"post"
        ]);
        $id          = gum::query("id");
        $cover       = gum::query("cover", ["strip_tags" => true]);
        $cover_id    = gum::query("cover_id", ["int" => true]);
        $title       = gum::query("title", ["strip_tags" => true]);
        $content     = gum::query("content");
        $author      = gum::query("author", ["strip_tags" => true]);
        $keywords    = gum::query("keywords", ["strip_tags" => true]);
        $description = gum::query("description", ["strip_tags" => true]);
        $best        = gum::query("best", ["int" => true]);
        $status      = gum::query("status", ["int" => true]);
        $tag_id      = gum::query("tag_id", ["int" => true]);
        $tag_name    = gum::query("tag_name", ["strip_tags" => true]);

        if ($title == "" || $content == "" || $tag_id == "" || $tag_name == "") {
            gum::json(["code" => 400]);
        }
        $file_ids = gum::query("file_ids");

        $action = false;
        $data   = [
            "cover"       => $cover,
            "cover_id"    => $cover_id,
            "title"       => $title,
            "content"     => $content,
            "author"      => $author,
            "keywords"    => $keywords,
            "description" => $description,
            "best"        => $best,
            "status"      => $status,
            "tag_id"      => $tag_id,
            "tag_name"    => $tag_name,

        ];
        if ($id == "") {
            $data["time"]        = time();
            $data["update_time"] = 0;
            $action              = $this->db->insert("post", $data);
            $id                  = $this->db->id();
        } else {
            $data["update_time"] = time();
            $action              = $this->db->update("post", $data, "id=$id");
            upload::remove_bind($this->db, "post", $id);
        }
        // 缩图绑定
        if ($cover_id != "") {
            upload::bind($this->db, "post", $id, $cover_id);
        }
        upload::bind($this->db, "post", $id, $file_ids);

        if ($action) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }

    // 删除帖子
    function delete() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"post"
        ]);
        $ids = gum::query("ids");

        if ($ids == "") {
            gum::json(["code" => 400]);
        }
        $ids_array = explode(",", $ids);
        foreach ($ids_array as $id) {
            tag::remove_bind($this->db, "post", $id);
            upload::remove_bind($this->db, "post", $id);
        }
        $success = $this->db->delete("post", "id IN ($ids)");

        if ($success) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }
}