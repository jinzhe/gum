<?php
if (!class_exists('user')) {

    class user {
        // 输出可显示的json字段
        const FIELDS = 'id,account,nickname,photo,level,permission,tel,tel_verification,email,email_verification,status';
        // 依赖文件
        static function depend() {
            return ['upload'];
        }

        static function init() {
            new user();
        }

        function __construct() {
            $this->db = new db();
            gum::init([
                "bind" => $this,
            ]);
        }

        // 获取数据列表
        function search() {
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
                $ids         = implode(",",array_column($rows, 'id')); //取出id集合字符串
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
        static function check($db, $options = []) {
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
        function get() {
            user::check($this->db, ["level" => 255]);
            $id     = gum::query("id");
            $result = $this->db->row("SELECT " . self::FIELDS . " FROM user WHERE id=" . $id);
            if ($result == false) {
                gum::json(["code" => 500]);
            }
            gum::json(["code" => 200, "result" => $result]);
        }
        // 获取一个用户信息(前端)
        function get_info() {
            $token  = gum::query("token");
            $result = $this->db->row("SELECT " . self::FIELDS . " FROM user WHERE status=1 AND token='" . $token . "'");
            if ($result == false) {
                gum::json(["code" => 500]);
            }
            gum::json(["code" => 200, "result" => $result]);
        }

        // 获取会员信息
        static function info($db) {
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
        function login() {
            // if (!STATUS_LOGIN) {
            //     gum::json(["code" => 0, "info" => "登陆服务关闭"]);
            // }
            $account  = gum::query("account");
            $password = gum::query("password");
            // 过滤登录字符串
            if ($account == "" || $password == "") {
                gum::json(["code" => 400, "info" => "账号密码不能为空"]);
            }

            $row = $this->db->row("SELECT " . self::FIELDS . " FROM user WHERE (account='" . $account . "' OR email='" . $account . "' OR tel='" . $account . "') AND password='" . gum::hash($password) . "' AND status=1");
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
        function logout() {
            user::check($this->db);
            $token = gum::query("token");
            $this->db->update("user", ["token" => ""], "token='" . $token . "'");
        }

        // 注册
        function join() {
            // if (!STATUS_JOIN) {
            //     gum::json(["code" => 0, "info" => "注册服务关闭"]);
            // }
            $account  = gum::query("account");
            $password = gum::query("password");
            $nickname = gum::query("nickname");
            $token    = gum::query("token");
            $code     = gum::query("code");
            if ($account == "" || $password == "" || $nickname == "" || $token == "" || $code == "") {
                gum::json(["code" => 400, "info" => "缺少参数"]);
            }
            // 判断是否在code表中
            $row = $this->db->row("SELECT * FROM user_code WHERE value='$account' AND code='$code' AND token='$token' AND status=0");
            if (!$row) {
                gum::json(["code" => 401, "info" => "非法注册"]);
            }
            $uuid   = gum::uuid();
            $action = false;
            $data   = [
                "token"      => $uuid,
                "account"    => $account,
                "password"   => gum::hash($password),
                "level"      => 0,
                "permission" => "",
                "nickname"   => $nickname,
                "photo"      => "",
                "email"      => "",
                "tel"        => $account,
                "join_ip"    => gum::ip(),
                "join_time"  => time(),
                "login_ip"   => gum::ip(),
                "login_time" => time(),
                "status"     => 1,
            ];
            $action = $this->db->insert("user", $data);
            $this->db->update("user_code", ["status" => 1], "token='$token'");
            if ($action) {
                gum::json(["code" => 200, "token" => $uuid]);
            } else {
                gum::json(["code" => 500]);
            }
        }
        // 忘记密码 user_id code time type
        function forget() {
            $account  = gum::query("account");
            $password = gum::query("password");
            $token    = gum::query("token");
            $code     = gum::query("code");
            if ($account == "" || $token == "" || $code == "") {
                gum::json(["code" => 400, "info" => "参数丢失"]);
            }
            // 检查是否存在这个账号
            $row = $this->db->row("SELECT id FROM user WHERE tel='$account'");
            if ($row == false) {
                gum::json(["code" => 501, "info" => "账号不存在"]);
            }

            // 判断是否在code表中
            $row = $this->db->row("SELECT * FROM user_code WHERE value='$account' AND code='$code' AND token='$token' AND status=0");
            if (!$row) {
                gum::json(["code" => 401, "info" => "非法账号"]);
            }
            $data = [
                "password" => gum::hash($password),
            ];
            $action = $this->db->update("user", $data, "tel='$account'");
            if ($action) {
                gum::json(["code" => 200]);
            } else {
                gum::json(["code" => 500]);
            }
        }

        // 绑定邮箱
        function bind_email() {
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
        function verify_email() {
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

        function send_code() {
            $type   = gum::query("type"); // 1.EMAIL 2.TEL,3.账号id
            $target = gum::query("target"); // 1.注册 2.忘记密码 3.更换手机
            $value  = gum::query("value"); //邮箱地址或者电话号码
            $id     = gum::query("id", "0"); //会员id

            if ($type == "" || $target == "" || $value == "") {
                gum::json(["code" => 400]);
            }
            if ($type == "1") {
                $type_name = "email";
            } elseif ($type == "2") {
                $type_name = "tel";
            } else {
                $type_name = "account";
            }
            // 注册的时候检测
            if ($type == "2" && $target == "1") {
                // 检查是否存在这个账号
                $is_join = $this->db->count("SELECT id FROM user WHERE $type_name='$value'");
                if ($is_join > 0) {
                    gum::json(["code" => 501, "info" => "账号已存在"]);
                }
            }
            // 更换手机时候
            if ($target == "3") {
                if ($id == "") {
                    gum::json(["code" => 400]);
                }
                // 检查是否存在这个账号
                $is_exists = $this->db->count("SELECT id FROM user WHERE id='$id'");
                if ($is_exists == 0) {
                    gum::json(["code" => 502, "info" => "账号不存在"]);
                }
            }

            // 检测发送验证码上限(每天10次)
            if ($type == "2") {
                $upper_limit = $this->db->count("SELECT * FROM user_code WHERE value='$value' AND time>" . (time() - (3600 * 24)));
                if ($upper_limit >= 10) {
                    gum::json(["code" => 502, "info" => "已达到每日发送上限"]);
                }
            }
            $code   = mt_rand(1000, 9999);
            $token  = gum::uuid();
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
                gum::json(["code" => 200, "token" => $token]);
            } else {
                gum::json(["code" => 500, "info" => "注册失败"]);
            }
        }

        // 绑定手机号
        function verify_tel() {
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
        function change_password() {
            user::check($this->db);
            $token = gum::query("token");
            $old   = gum::query("old");
            $new   = gum::query("new");
            if (empty($old) || empty($new)) {
                gum::json(["code" => 401, "info" => "密码不能为空"]);
            }
            // 旧密码验证
            $row = $this->db->row("SELECT password FROM user WHERE token='$token'");
            // var_dump($row);exit;
            if ($row == false || gum::hash($old) != $row["password"]) {
                gum::json(["code" => 401, "info" => "旧密码不正确"]);
            }

            $data = [
                "password" => gum::hash($new),
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
        // 更换密码（管理员）
        function update_password() {
            user::check($this->db, ["level" => 255]);
            $token = gum::query("token");
            $old   = gum::query("old");
            $new   = gum::query("new");
            $row   = $this->db->row("SELECT password FROM user WHERE token='$token'");
            if ($row["password"] != gum::hash($old)) {
                gum::json(["code" => 501, "info" => "旧密码不正确"]);
            }
            $data = [
                "password" => gum::hash($new),
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
        function save() {

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
        function delete() {
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