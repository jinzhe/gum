<?php
class db {
	public $link;

	function __construct($dbname, $password = '123456', $user = 'root', $host = '127.0.0.1', $port = 3306, $charset = 'utf8') {
		try {
			$this->link = new PDO("mysql:host=$host;dbname=$dbname;port=$port;charset=$charset", $user, $password, array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //默认是PDO::ERRMODE_SILENT, 0, (忽略错误模式)
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 默认是PDO::FETCH_BOTH, 4
			));
		} catch (PDOException $e) {
			die($e->getMessage());
		}
		//$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    //设置异常处理方式
		//$this->link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);   //设置默认关联索引遍历
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

	function update($table, $values, $condition = '') {
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
		$tables = $this->row("SHOW TABLES", PDO::FETCH_NUM);
		$sql = '';
		foreach ($tables as $v) {
			$sql .= "DROP TABLE IF EXISTS `$v`;\n";
			$rs = $this->row("show create table $v");
			$sql .= $rs[1] . ";\n\n";
		}
		foreach ($tables as $v) {
			while ($rs = $this->rows("select * from $v")) {
				$comma = "";
				$sql .= "INSERT INTO $v values(";
				for ($i = 0; $i < $fild; $i++) {
					$sql .= $comma . "'" . mysql_escape_string($rs[$i]) . "'";
					$comma = ",";
				}
				$sql .= ");\n";
			}
			$sql .= "\n";
		}
		return $sql;
	}
}