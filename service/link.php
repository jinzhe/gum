<?php
/*
 * 链接服务
 *
 * 说明：用于击跳到相应网址
 * 相关表：link
 */

class link {
    // 依赖文件
    public static function depend() {
        return [
            "user",
        ];
    }
 
    public static function init() {
        new link();
    }

    function __construct() {
        $this->db = new db();
        gum::init([
            "bind" => $this,
        ]);
    }
// 根据关键字搜索列表
    function search() {
        $page_index = (int) gum::query("page_index", 1);
        $page_size  = (int) gum::query("page_size", 20);
        $keyword    = gum::query("keyword");
        $orderby    = gum::query("orderby", "sort");
        $sortby     = gum::query("sortby", "ASC");

        $sql = "SELECT * FROM link WHERE 1=1";

        if ($keyword != "") {
            $sql .= " AND (INSTR(title,'" . $keyword . "') OR INSTR(url,'" . $keyword . "') OR INSTR(tag,'" . $keyword . "'))";
 
        }

        $total = $this->db->count($sql); //先查数量
        $data  = [];
        if ($total > 0) {
            $page_index = max(min($page_index, ceil($total / $page_size)), 1);
            $rows       = $this->db->rows($sql . " ORDER BY $orderby $sortby LIMIT " . (($page_index - 1) * $page_size) . "," . $page_size); //获取ID(索引)
            $ids        = implode(array_column($rows, 'id'), ","); //取出id集合字符串

            $data = $this->db->rows("SELECT * FROM link WHERE id IN (" . $ids . ") ORDER BY $orderby $sortby");
        }
        gum::json([
            "code"   => 200,
            "result" => [
                "total" => $total,
                "data"  => $data,
            ],
        ]);
    }

    // 添加 & 更新
    function save() {
        user::check($this->db, ["level" => 255]);

        $id     = gum::query("id");
        $image  = gum::query("image");
        $title  = gum::query("title");
        $url    = gum::query("url");
        $tag    = gum::query("tag");
        $status = gum::query("status", "1");

        if ($title == "" || $url == "") {
            gum::json(["code" => 400, "info" => "未填写完整"]);
        }
        $action = false;
        $data   = [
            "image"  => $image,
            "title"  => $title,
            "url"    => $url,
            "tag"    => $tag,
            "status" => $status,
        ];
        if ($id == "") {
            $data["time"] = time();
            $data["sort"] = 0;
            $action       = $this->db->insert("link", $data);
        } else {
            $action = $this->db->update("link", $data, "id=$id");
        }
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
        $success = $this->db->delete("link", "id=" . $id);
        if ($success) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }
    // 更新排序
    function update_sort() {
        user::check($this->db, ["level" => 255]);
        $ids = gum::query("ids");
        if ($ids == "") {
            gum::json(["code" => 400]);
        }
        $ids_list = explode(",", $ids);
        $sort     = 0;
        foreach ($ids_list as $id) {
            $this->db->update("link", "sort=$sort", "id=" . $id);
            $sort++;
        }

        gum::json(["code" => 200]);

    }
}