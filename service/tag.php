<?php
class tag {
    public static function depend() {
        return [
            "user",
        ];
    }
    public static function init() {
        new tag();
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

        $sql = "SELECT * FROM tag WHERE 1=1";

        if ($keyword != "") {

            $sql .= " AND INSTR(name,'" . $keyword . "')";
        }

        $total = $this->db->count($sql); //先查数量
        $data  = [];
        if ($total > 0) {
            $page_index = max(min($page_index, ceil($total / $page_size)), 1);
            $rows       = $this->db->rows($sql . " ORDER BY $orderby $sortby LIMIT " . (($page_index - 1) * $page_size) . "," . $page_size); //获取ID(索引)
            $ids        = implode(",",array_column($rows, 'id')); //取出id集合字符串
            $data       = $this->db->rows("SELECT * FROM tag WHERE id IN (" . $ids . ") ORDER BY $orderby $sortby");
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
    // {"hot":10,"ids":"1,2,3,4"}
    function data() {
        $data = gum::query("data");
        if ($data == "") {
            gum::json(["code" => 500, "info" => "格式错误"]);
        }
        $data = json_decode($data);
        if ($data == false) {
            gum::json(["code" => 500, "info" => "格式错误"]);
        }
        $result = [];

        if (isset($data->hot)) {
            $result["hot"] = $this->db->rows("SELECT id,name,count FROM tag  ORDER BY count DESC LIMIT " . $data->hot);
        }
        if (isset($data->ids)) {
            $result["ids"] = $this->db->rows("SELECT id,name,count FROM tag WHERE id IN(" . $data->ids . ")");
        }
        if (isset($data->bind_id) && isset($data->bind_type)) {
            $result["bind"] = $this->db->rows("SELECT a.id,a.name FROM tag AS a LEFT JOIN tag_bind AS b ON a.id=b.id WHERE b.bind_id=$data->bind_id AND b.bind_type='$data->bind_type'");
        }
        gum::json(["code" => 200, "result" => $result]);
    }

    // 查找一个tag中的业务ID集合
    function bindids($id, $type) {
        $rows = $this->db->rows("SELECT bind_id FROM tag_bind WHERE id=? AND bind_type=?", [
            "params" => [$id, $type],
        ]);
        $ids = implode(",", array_unique(array_column($rows, 'bind_id')));
        return $ids;
    }

    // 创建 & 更新 帖子
    function save() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"tag"
        ]);
        $id     = gum::query("id");
        $name   = gum::query("name");
        $tag    = gum::query("tag");
        $status = gum::query("status");
        if ($name == "") {
            gum::json(["code" => 400]);
        }
        $action = false;
        $data   = [
            "name"   => $name,
            "tag"    => $tag,
            "status" => $status,
        ];
        if ($id == "") {
            $action = $this->db->insert("tag", $data);
            $id     = $this->db->id();
        } else {
            $action = $this->db->update("tag", $data, "id=$id");
        }
        if ($action) {
            gum::json(["code" => 200, "id" => $id]);
        } else {
            gum::json(["code" => 500]);
        }
    }

    // 删除帖子
    function delete() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"tag"
        ]);

        $id = gum::query("id");
        if ($id == "") {
            gum::json(["code" => 400]);
        }
        $this->db->delete("tag_bind", "id=" . $id);
        $success = $this->db->delete("tag", "id=" . $id);
        if ($success) {
            gum::json(["code" => 200]);
        } else {
            gum::json(["code" => 500]);
        }
    }

    // 绑定
    public static function bind($db, $bind_type, $bind_id, $ids = "") {
        $ids = explode(",", $ids);
        foreach ($ids as $id) {
            $db->insert("tag_bind", [
                "id"        => $id,
                "bind_id"   => $bind_id,
                "bind_type" => $bind_type,
            ]);
            $db->update("tag", "count=count+1", "id=" . $id);
        }
    }
    // 解除绑定
    public static function remove_bind($db, $bind_type, $bind_id, $ids = "") {
        if ($ids != "") {
            $where = "id IN ($ids)";
        } else {
            $where = "bind_id='$bind_id' AND bind_type='$bind_type'";
        }
        $db->delete("tag", "count=count-1", $where);
        $db->delete("tag_bind", $where);
    }

    // 更新排序
    function update_sort() {
        user::check($this->db, [
            "level" => 255,
            "permission"=>"tag"
        ]);
        $ids = gum::query("ids");
        if ($ids == "") {
            gum::json(["code" => 400]);
        }
        $ids_list = explode(",", $ids);
        $sort     = 0;
        foreach ($ids_list as $id) {
            $this->db->update("tag", "sort=$sort", "id=" . $id);
            $sort++;
        }

        gum::json(["code" => 200]);

    }
}