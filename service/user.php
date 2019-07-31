<?php
if (!class_exists('user')) {

    class user {
        // 输出可显示的json字段
        const FIELDS = 'id,account,nickname,photo,level,permission,tel,tel_verification,email,email_verification,status';
        // 依赖文件
        public static function depend() {
            return ['upload'];
        }

        public static function install() {
            return [
                "CREATE TABLE `user` (
                `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
                `account` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '自定义账号',
                `password` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码',
                `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0普通会员 255 超级管理',
                `nickname` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '昵称',
                `photo` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
                `email` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '邮箱',
                `email_verification` tinyint(1) NOT NULL DEFAULT '0',
                `tel` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机',
                `tel_verification` tinyint(1) NOT NULL DEFAULT '0',
                `join_ip` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
                `join_time` int(10) unsigned NOT NULL DEFAULT '0',
                `login_ip` varchar(50) CHARACTER SET utf8 NOT NULL,
                `login_time` int(10) unsigned NOT NULL DEFAULT '0',
                `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `token` varchar(100) CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;",
            ];
        }

        public static function init() {
            new user();
        }

        public function __construct() {
            $this->db = new db();
            gum::init([
                "bind" => $this,
            ]);
        }

        // 获取数据列表
        public function search() {
            user::check($this->db);
            $page_index = (int)gum::query("page_index", 1);
            $page_size  = (int)gum::query("page_size", 20);
            $fields     = gum::query("fields");
            $begin      = gum::query("begin");
            $end        = gum::query("end");
            $status     = gum::query("status");
            $keyword    = gum::query("keyword");
            $orderby    = gum::query("orderby", "id");
            $sortby     = gum::query("sortby", "DESC");
            $user       = self::info($this->db);
            if ($user && $user["level"] == 255) {
                $sql = "SELECT * FROM user WHERE 1=1";
                if ($status != "") {
                    $sql .= " AND status=$status";
                }
            } else {
                $sql = "SELECT * FROM user WHERE status=1 AND level=0";
            }

            // 关键字搜索
            if ($keyword != "") {
                // $sql .= " AND nickname LIKE '%" . $keyword . "%'";
                $sql .= " AND ( INSTR(account,'" . $keyword . "') OR INSTR(nickname,'" . $keyword . "') OR INSTR(tel,'" . $keyword . "') OR INSTR(email,'" . $keyword . "'))";
            }
            if ($begin != "") {
                $sql .= " AND join_time>=$begin";
            }
            if ($end != "") {
                $sql .= " AND join_time<=$end";
            }
            // echo $sql;exit;
            $count = $this->db->count($sql); //先查数量
            $data  = [];
            if ($count > 0) {
                $page_index  = max(min($page_index, ceil($count / $page_size)), 1);
                $rows        = $this->db->rows($sql . " ORDER BY $orderby $sortby LIMIT " . (($page_index - 1) * $page_size) . "," . $page_size); //获取ID(索引)
                $ids         = implode(array_column($rows, 'id'), ","); //取出id集合字符串
                $safe_fields = [
                    'id',
                    'account',
                    'level',
                    'nickname',
                    'photo',
                    'email',
                    'email_verification',
                    'tel',
                    'tel_verification',
                    'join_ip',
                    'join_time',
                    'login_ip',
                    'login_time',
                ];
                if ($user && $user["level"] == 255) {
                    $safe_fields[] = 'status';
                    $safe_fields[] = 'permission';
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
                $data = $this->db->rows("SELECT " . $fields . " FROM user WHERE id IN (" . $ids . ") ORDER BY $orderby $sortby");
            }
            gum::json([
                "code"   => 200,
                "result" => [
                    "count" => $count,
                    "data"  => $data,
                ],
            ]);
        }

        // 验证权限
        public static function check($db, $options = []) {
            $token = gum::query("token");
            if ($token == "") {
                gum::json(["code" => 403, "info" => "token is empty"]);
            }
            if (isset($options["level"])) {
                $sql = "SELECT id,login_time,permission FROM user WHERE token='$token' AND level>=" . $options["level"];
            } else {
                $sql = "SELECT id,login_time FROM user WHERE token='$token'";
            }

            $row = $db->row($sql);

            if (isset($options["bool"])) {
                return !!$row;
            }
            // 不存在过期
            if ($row == false) {
                gum::json(["code" => 403]);
            }
            // 验证权限
            if (isset($options["level"]) && isset($options["permission"]) && $options["level"] == 255) {
                if ($row["permission"] != "super") {
                    $permissions = explode(",", $row["permission"]);
                    if (!in_array($options["permission"], $permissions)) {
                        gum::json(["code" => 403]);
                    }
                }
            }
            // 7天自动过期
            if (time() - $row["login_time"] > 7 * 24 * 3600) {
                gum::json(["code" => 403]);
            }
        }

        // 获取一个用户信息
        public function get() {
            user::check($this->db, ["level" => 255]);
            $id     = gum::query("id");
            $result = $this->db->row("SELECT " . self::FIELDS . " FROM user WHERE id=" . $id);
            if ($result == false) {
                gum::json(["code" => 500]);
            }
            gum::json(["code" => 200, "result" => $result]);
        }
        // 获取一个用户信息(前端)
        public function get_info() {
            $token  = gum::query("token");
            $result = $this->db->row("SELECT " . self::FIELDS . " FROM user WHERE status=1 AND token='" . $token . "'");
            if ($result == false) {
                gum::json(["code" => 500]);
            }
            gum::json(["code" => 200, "result" => $result]);
        }

        // 获取会员信息
        public static function info($db) {
            $token = gum::query("token");
            if ($token == "") {
                return [];
            }

            $row = $db->row("SELECT * FROM user WHERE token=?", [
                "params" => [$token],
            ]);
            return $row;
        }
        // 登录
        public function login() {
            // if (!STATUS_LOGIN) {
            //     gum::json(["code" => 0, "info" => "登陆服务关闭"]);
            // }
            $account  = gum::query("account");
            $password = gum::query("password");
            // 过滤登录字符串
            if ($account == "" || $password == "") {
                gum::json(["code" => 400, "info" => "账号密码不能为空"]);
            }

            $row = $this->db->row("SELECT " . self::FIELDS . " FROM user WHERE (account='".$account."' OR email='".$account."' OR tel='".$account."') AND password='".gum::hash($password)."' AND status=1");
            // print_r($row);exit;
            if ($row != false) {
                $token = gum::uuid();
                $this->db->update("user", [
                    "token"      => $token,
                    "login_ip"   => gum::ip(),
                    "login_time" => time(),
                ], "id='" . $row["id"] . "'");
                gum::json([
                    "code"   => 200,
                    "result" => $row,
                    "token"  => $token,
                ]);
            } else {
                gum::json(["code" => 404]);
            }
        }

        // 登出
        public function logout() {
            user::check($this->db);
            $token = gum::query("token");
            $this->db->update("user", ["token" => ""], "token='" . $token . "'");
        }

        // 注册
        public function join() {
            // if (!STATUS_JOIN) {
            //     gum::json(["code" => 0, "info" => "注册服务关闭"]);
            // }
            $account  = gum::query("account");
            $password = gum::query("password");
            $status   = gum::query("status", "1");
            if ($account == "" || $password == "") {
                gum::json(["code" => 400]);
            }
            $token  = gum::uuid();
            $action = false;
            $data   = [
                "token"      => $token,
                "account"    => $account,
                "password"   => gum::hash($password),
                "level"      => 0,
                "nickname"   => "",
                "photo"      => "",
                "email"      => "",
                "tel"        => "",
                "join_ip"    => gum::ip(),
                "join_time"  => time(),
                "login_ip"   => gum::ip(),
                "login_time" => time(),
                "status"     => $status,
            ];
            $action = $this->db->insert("user", $data);
            if ($action) {
                gum::json(["code" => 200, "token" => $token]);
            } else {
                gum::json(["code" => 500]);
            }
        }
        // 忘记密码 user_id code time type
        public function forget() {
            $account = gum::query("account");
            if ($account == "") {
                gum::json(["code" => 400, "info" => "账号不能为空"]);
            }
            // 检查是否存在这个账号
            $row = $this->db->row("SELECT id FROM user WHERE account='$account'");
            if ($row == false) {
                gum::json(["code" => 501, "info" => "账号不存在"]);
            }
            $type  = gum::query("type", "email"); //email or tel
            $value = gum::query("value");
            if ($type == "" || $value == "") {
                gum::json(["code" => 400]);
            }
            if ($type == "email") {
                $code = gum::uuid();
            } elseif ($type == "tel") {
                $code = mt_rand(1000, 9999); //手机验证码
            }

            $action = false;
            $data   = [
                "type"    => $type,
                "code"    => $code,
                "time"    => time(),
                "status"  => 0,
                "user_id" => $row["id"],

            ];
            $action = $this->db->insert("user_verification", $data);

            if ($type == "email") {
                gum::mail([
                    "to"       => $value,
                    "subject"  => "找回密码",
                    "body"     => "$code",
                    "from"     => SMTP_USER,
                    "server"   => SMTP_SERVER,
                    "port"     => SMTP_PORT,
                    "user"     => SMTP_USER,
                    "password" => SMTP_PASSWORD,
                ]);
            } elseif ($type == "tel") {
                gum::sms([
                    "to"      => $value,
                    "content" => "$code",
                ]);
            }

            if ($action) {
                gum::json(["code" => 200]);
            } else {
                gum::json(["code" => 500]);
            }
        }

        // 绑定邮箱
        public function bind_email() {
            user::check($this->db);

            $id    = gum::query("id");
            $email = gum::query("email");
            if ($email == "" || $id == "") {
                gum::json(["code" => 400]);
            }
            // 检查是否存在这个账号
            $row = $this->db->row("SELECT id FROM user WHERE id='$id'");
            if ($row == false) {
                gum::json(["code" => 501, "info" => "账号不存在"]);
            }
            $code = gum::uuid();

            $action = false;
            $data   = [
                "type"    => "email",
                "code"    => $code,
                "time"    => time(),
                "status"  => 0,
                "user_id" => $id,
            ];
            $action = $this->db->insert("user_verification", $data);
            gum::mail([
                "to"       => $value,
                "subject"  => "验证绑定邮箱",
                "body"     => "$code",
                "from"     => SMTP_USER,
                "server"   => SMTP_SERVER,
                "port"     => SMTP_PORT,
                "user"     => SMTP_USER,
                "password" => SMTP_PASSWORD,
            ]);

            if ($action) {
                gum::json(["code" => 200]);
            } else {
                gum::json(["code" => 500]);
            }
        }

        // 邮箱绑定验证
        public function verify_email() {
            $code = gum::query("code");
            // 检查是否存在这个账号
            $row = $this->db->row("SELECT * FROM user_verification WHERE code='$code'");
            if ($row == false) {
                echo ("非法参数");
            } else {
                // 更新这个邮箱为已经验证
                $this->db->update("user", ["email_verification" => 1], "id='" . $row["user_id"] . "'");
                echo "验证绑定邮箱成功";
            }
        }

        // 发送短信验证码入库

        public function send_code() {
            $type   = gum::query("type"); // 1.EMAIL 2.TEL
            $target = gum::query("target"); // 1.注册 2.忘记密码 3.更换手机
            $value  = gum::query("value"); //邮箱地址或者电话号码
            $id     = gum::query("id", "0"); //会员id

            if ($value == "") {
                gum::json(["code" => 400]);
            }

            // 更换手机时候
            if ($target == "3") {
                if ($id == "") {
                    gum::json(["code" => 400]);
                }
                // 检查是否存在这个账号
                $row = $this->db->row("SELECT id FROM user WHERE id='$id'");
                if ($row == false) {
                    gum::json(["code" => 501, "info" => "账号不存在"]);
                }
            }

            $code = mt_rand(1000, 9999);
            $token=gum::uuid();
            $action = false;
            $data   = [
                "type"    => $type,
                "target"  => $target,
                "code"    => $code,
                "value"   => $value,
                "token"   => $token,
                "time"    => time(),
                "status"  => 0,
                "user_id" => $id,
            ];
            $action = $this->db->insert("user_code", $data);
            // 发送短信
            if ($type == "2") {
                // gum::sms([
                //     "to"      => $tel,
                //     "content" => "$code",
                // ]);
            }
            if ($action) {
                gum::json(["code" => 200,"token"=>$token]);
            } else {
                gum::json(["code" => 500]);
            }
        }

        // 绑定手机号
        public function verify_tel() {
            $tel  = gum::query("tel");
            $code = gum::query("code");
            // 检查是否存在这个账号
            $row = $this->db->row("SELECT * FROM user_verification WHERE value='$tel' AND code='$code' AND time>" . time() - 7200);
            if ($row == false) {
                echo ("非法参数");
            } else {
                // 更新这个邮箱为已经验证
                $this->db->update("user", [
                    "tel_verification" => 1,
                ], "id='" . $row["user_id"] . "'");
                echo "验证绑定邮箱成功";
            }
        }

        // 更换密码（用户）
        public function change_password() {
            user::check($this->db);

            $id          = gum::query("id");
            $token       = gum::query("token");
            $oldPassword = gum::query("oldPassword");
            $newPassword = gum::query("newPassword");

            // 旧密码验证
            $row = $this->db->row("SELECT password FROM user WHERE token='$token'");
            // var_dump($row);exit;
            if ($row == false || $oldPassword != $row["password"]) {
                gum::json(["code" => 401]);
            }
            $action = false;
            $data   = [
                "password" => gum::hash($newPassword),
            ];
            $action = $this->db->update("user", $data, "token='$token'");
            $json   = [];
            if ($action) {
                $json["code"] = 200;
            } else {
                $json["code"] = 500;
            }
            gum::json($json);
        }
        // 更换密码（用户）
        public function update_password() {
            user::check($this->db, ["level" => 255]);

            $id       = gum::query("id");
            $token    = gum::query("token");
            $password = gum::query("password");

            $action = false;
            $data   = [
                "password" => gum::hash($password),
            ];
            $action = $this->db->update("user", $data, "token='$token'");
            $json   = [];
            if ($action) {
                $json["code"] = 200;
            } else {
                $json["code"] = 500;
                $json["info"] = "修改失败";
            }
            gum::json($json);
        }
        // 创建 & 更新
        public function save() {

            user::check($this->db, [
                "level"      => 255,
                "permission" => "super",
            ]);

            $id                 = gum::query("id");
            $account            = gum::query("account");
            $nickname           = gum::query("nickname");
            $photo              = gum::query("photo");
            $password           = gum::query("password");
            $level              = gum::query("level");
            $permission         = gum::query("permission");
            $email              = gum::query("email");
            $tel                = gum::query("tel");
            $email_verification = gum::query("email_verification");
            $tel_verification   = gum::query("tel_verification");
            $status             = gum::query("status");
            if ($account == "" || $nickname == "") {
                gum::json(["code" => 400]);
            }

            $action = false;
            $data   = [
                "nickname"           => $nickname,
                "email"              => $email,
                "tel"                => $tel,
                "email_verification" => $email_verification,
                "tel_verification"   => $tel_verification,
                "status"             => $status,
                "photo"              => $photo,
                "level"              => $level,
                "permission"         => $permission,
            ];

            if ($password != "") {
                $data["password"] = gum::hash($password);
            }
            // print_r($data);exit;
            if ($id == "") {
                $data["account"]    = $account;
                $data["join_time"]  = time();
                $data["join_ip"]    = gum::ip();
                $data["login_time"] = time();
                $data["login_ip"]   = gum::ip();
                $data["token"]      = "";
                $action             = $this->db->insert("user", $data);
                $id                 = $this->db->id();

            } else {
                $action = $this->db->update("user", $data, "id=$id");
                upload::remove_bind($this->db, "user", $id);
            }
            $photo_id = gum::query("photo_id");
            if ($photo_id != "") {
                upload::bind($this->db, "user", $id, $photo_id);
            }
            if ($action) {
                gum::json(["code" => 200]);
            } else {
                gum::json(["code" => 500]);
            }
        }
        // 删除
        public function delete() {
            user::check($this->db, [
                "level"      => 255,
                "permission" => "super",
            ]);

            $ids = gum::query("ids");

            if ($ids == "") {
                gum::json(["code" => 400]);
            }

            $success = $this->db->delete("user", "id IN ($ids)");

            if ($success) {
                gum::json(["code" => 200]);
            } else {
                gum::json(["code" => 500]);
            }
        }
    }
}