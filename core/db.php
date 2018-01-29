<?php
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
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //默认是PDO::ERRMODE_SILENT, 0, (忽略错误模式)
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 默认是PDO::FETCH_BOTH, 4
				));
				break;
			}

		} catch (PDOException $e) {
			die($e->getMessage());
		}
		//$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    //设置异常处理方式
		//$this->link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);   //设置默认关联索引遍历
	}

	public static function create($type = DB_TYPE, $db = DB_NAME, $password = DB_PASSWORD, $user = DB_USER, $host = DB_HOST, $port = DB_PORT, $charset = 'utf8') {
		$p = new PDO("mysql:host=$host;port=$port;charset=$charset", $user, $password);
		$p->query("CREATE DATABASE $db");
		return new db($type, $db, $password, $user, $host, $port, $charset);
	}

	function row($sql, $mode = PDO::FETCH_ASSOC) {
		$stmt = $this->link->query($sql);
		return $stmt->fetch($mode);
	}

	function rows($sql, $mode = PDO::FETCH_ASSOC) {
		$stmt = $this->link->query($sql);
		return $stmt->fetchAll($mode);
	}

	function count($sql) {
		$stmt = $this->link->query($sql);
		return $stmt->rowCount();
	}

	function insert($table, $values) {
		$ks = '';
		$vs = '';
		foreach ($values as $key => $value) {
			$ks .= $ks ? ",`$key`" : "`$key`";
			$vs .= $vs ? ",'$value'" : "'$value'";
		}
		$sql = "INSERT INTO `$table` ($ks) VALUES ($vs)";
		// var_dump($sql);
		try {
			$affect = $this->exec($sql);
			if ($affect > 0) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	function update($table, $values, $condition = '1=1') {
		$v = '';
		if (is_string($values)) {
			$v .= $values;
		} else {
			foreach ($values as $key => $value) {
				$v .= $v ? ",`$key`='$value'" : "`$key`='$value'";
			}
		}
		$sql = "UPDATE `$table` SET $v  WHERE $condition";
		try {
			$affect = $this->exec($sql);
			if ($affect > 0) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	function delete($table, $condition = '') {
		if (empty($condition) || $condition == '') {
			$sql = "delete from $table";
		} else {
			$sql = "delete from $table where $condition";
		}
		try {
			$affect = $this->exec($sql);
			if ($affect > 0) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
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
		$tables = [];
		$tables = $this->rows("SHOW TABLES", PDO::FETCH_NUM);
		$tables = array_column($tables, 0);
		$sql = '';
		foreach ($tables as $v) {
			$sql .= "DROP TABLE IF EXISTS `$v`;\n";
			$rs = $this->row("show create table $v");

			$sql .= $rs["Create Table"] . ";\n\n";
		}
		foreach ($tables as $v) {
			$stmt = $this->link->query("select * from $v");
			$rows = $stmt->fetchAll(PDO::FETCH_NUM);
			$columnCount = $stmt->columnCount();
			foreach ($rows as $row) {
				$comma = "";
				$sql .= "INSERT INTO $v values(";
				for ($i = 0; $i < $columnCount; $i++) {
					$sql .= $comma . "'" . addslashes($row[$i]) . "'";
					$comma = ",";
				}
				$sql .= ");\n";
			}
			$sql .= "\n";
		}
		return $sql;
	}
}