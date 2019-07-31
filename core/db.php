<?php
// last update 2019/03/21
class db {
    public $link;
    function __construct($type = DB_TYPE, $db = DB_NAME, $password = DB_PASSWORD, $user = DB_USER, $host = DB_HOST, $port = DB_PORT, $charset = 'utf8') {
        try {
            switch ($type) {
            case 'sqlite':
                $this->link = new PDO("sqlite:" . $db);
                break;
            default:
                $this->link = new PDO("mysql:host=$host;dbname=$db;port=$port;charset=$charset", $user, $password, array(
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //默认是PDO::ERRMODE_SILENT, 0, (忽略错误模式)
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 默认是PDO::FETCH_BOTH, 4
                    PDO::ATTR_EMULATE_PREPARES   => false, //禁用预准备语句的模拟
                    PDO::ATTR_STRINGIFY_FETCHES  => false, //禁止数字转成字符串
                ));

                break;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public static function create($type = DB_TYPE, $db = DB_NAME, $password = DB_PASSWORD, $user = DB_USER, $host = DB_HOST, $port = DB_PORT, $charset = 'utf8mb4') {
        $p = new PDO("mysql:host=$host;port=$port;charset=$charset", $user, $password);
        $p->query("CREATE DATABASE $db");
        return new db($type, $db, $password, $user, $host, $port, $charset);
    }

    function columns($table) {
        $stmt = $this->link->query("DESCRIBE $table");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function row($sql, $options = []) {
        $mode = PDO::FETCH_ASSOC;
        if (isset($options["mode"])) {
            $mode = $options["mode"];
        }
        if (isset($options["params"])) {
            $stmt = $this->link->prepare($sql);
            $stmt->execute($options["params"]);
        } else {
            $stmt = $this->link->query($sql);
        }
        return $stmt->fetch($mode);
    }

    function rows($sql, $options = []) {
        $mode = PDO::FETCH_ASSOC;
        if (isset($options["mode"])) {
            $mode = $options["mode"];
        }
        if (isset($options["params"])) {
            $stmt = $this->link->prepare($sql);
            $stmt->execute($options["params"]);
        } else {
            $stmt = $this->link->query($sql);
        }
        return $stmt->fetchAll($mode);
    }

    function count($sql) {
        $stmt = $this->link->query($sql);
        return $stmt->rowCount();
    }
    function insert($table, $values, $debug = false) {
        $ks = '';
        $vs = '';
        foreach ($values as $key => $value) {
            $value = addslashes($value);
            $ks .= $ks ? ",`$key`" : "`$key`";
            $vs .= $vs ? ",'$value'" : "'$value'";
        }
        $sql = "INSERT INTO `$table` ($ks) VALUES ($vs);";
        if ($debug) {
            echo ($sql);exit;
        }
        try {
            $affect = $this->exec($sql);
            //
            // echo $affect;
            // exit;
            if ($affect > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    function update($table, $values, $condition = '1=1', $debug = false) {
        $v = '';
        if (is_string($values)) {
            $v .= $values;
        } else {
            foreach ($values as $key => $value) {
                $value = addslashes($value);
                $v .= $v ? ",`$key`='$value'" : "`$key`='$value'";
            }
        }
        $sql = "UPDATE `$table` SET $v  WHERE $condition;";
        if ($debug) {
            echo ($sql);exit;
        }
        try {
            $affect = $this->exec($sql);
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    function delete($table, $condition = '', $options = []) {
        if (empty($condition) || $condition == '') {
            $sql = "DELETE FROM $table";
        } else {
            $sql = "DELETE FROM $table WHERE $condition";
        }
        try {
            if (isset($options["params"])) {
                $stmt = $this->link->prepare($sql);
                $stmt->execute($options["params"]);
            } else {
                $stmt = $this->link->query($sql);
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    function exec($sql) {
        return $this->link->exec($sql);
    }
    function id() {
        return $this->link->lastInsertId();
    }
    function clear($tables = array()) {
        if (empty($tables)) {
            return false;
        }
        foreach ($tables as $key => $table) {
            $this->exec("TRUNCATE TABLE `$table`");
        }
    }
    function export() {
        $tables = array_column($this->rows("SHOW TABLES", [
            "mode" => PDO::FETCH_NUM,
        ]), 0);
        // print_r($tables);
        // exit;
        $sql = '';
        foreach ($tables as $v) {
            $sql .= "DROP TABLE IF EXISTS `$v`;\r\n\r\n\r\n";
            $rs = $this->row("SHOW CREATE TABLE $v");
            $sql .= str_replace("\n", "", $rs["Create Table"]) . ";\r\n\r\n\r\n";
        }
        foreach ($tables as $v) {
            // 获取字段名称
            $stmt = $this->link->prepare("DESC $v");
            $stmt->execute();
            $fields = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($fields as &$field) {
                $field = "`" . $field . "`";
            }

            $stmt        = $this->link->query("select * from $v");
            $rows        = $stmt->fetchAll(PDO::FETCH_NUM);
            $columnCount = $stmt->columnCount();
            foreach ($rows as $row) {
                $comma = "";
                $sql .= "INSERT INTO $v(" . implode(",", $fields) . ") values(";
                for ($i = 0; $i < $columnCount; $i++) {
                    $sql .= $comma . "'" . addslashes($row[$i]) . "'";
                    $comma = ",";
                }
                $sql .= ");\r\n\r\n\r\n";
            }
        }
        return $sql;
    }
}