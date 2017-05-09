<?php

namespace tea\db;

/**
 * 数据库类
 *
 * @package tea\db
 */
class Db {
	private static $_dbs = [];
	private $_dbId;

	/**
	 * @var \PDO
	 */
	private $_pdo;
	private $_config = [];
	private $_driver;

	/**
	 * 获取新的数据库操作对象
	 *
	 * @param string $dbId 数据库ID
	 * @param bool $connect 是否立即连接
	 * @return Db
	 */
	public static function db($dbId, $connect = true) {
		if (!isset(self::$_dbs[$dbId])) {
			$db = new Db($dbId, $connect);
			self::$_dbs[$dbId] = $db;
		}
		return self::$_dbs[$dbId];
	}

	/**
	 * 获取默认的数据库操作对象
	 *
	 * @return null|Db
	 */
	public static function defaultDb() {
		$db = o(":db.default.db");
		if ($db) {
			return self::db($db);
		}
		return null;
	}

	/**
	 * 构造器
	 *
	 * @param string $db 数据库ID
	 * @param bool $connect 是否立即连接
	 * @throws \Exception
	 */
	public function __construct($db, $connect = true) {
		if (is_scalar($db)) {
			//支持环境变量
			$db = str_replace('%{env}', TEA_ENV, $db);

			$config = o(":db.dbs");
			if(empty($config[$db])) {
				throw new \Exception("db '{$db}' was not configured");
			}

			$this->_dbId = $db;
			$config = $config[$db];
		}
		else if (is_array($db)) {
			$config = $db;
		}
		else {
			throw new \Exception("invalid db");
		}
		$this->_config = $config;

		if ($connect) {
			$this->reconnect();
		}
	}

	/**
	 * 取得配置信息
	 *
	 * @return array
	 */
	public function config() {
		return $this->_config;
	}

	/**
	 * 设置配置选项
	 *
	 * @param array $config 新的配置选项
	 */
	public function setConfig(array $config) {
		$this->_config = $config;
	}

	/**
	 * 取得数据库配置ID
	 *
	 * @return string
	 */
	public function id() {
		return $this->_dbId;
	}

	/**
	 * 取得数据库名称
	 *
	 * @return string
	 */
	public function name() {
		return $this->dsn()[1]["dbname"];
	}

	/**
	 * 取得DSN
	 *
	 * @return array
	 */
	public function dsn() {
		if (!isset($this->_config["dsn"])) {
			return [];
		}
		list($driver, $options) = explode(":", $this->_config["dsn"], 2);
		$dsn = [ $driver, [] ];
		foreach (explode(";", $options) as $option) {
			list($name, $value) = explode("=", $option, 2);
			$dsn[1][$name] = $value;
		}
		return $dsn;
	}

	/**
	 * 给数据加入引号，以便于写入数据库
	 *
	 * @param $data
	 * @return int|string
	 */
	public function quote($data) {
		if (is_numeric($data) && is_finite($data)) {
			return $data;
		}
		if (is_bool($data)) {
			return $data ? 1 : 0;
		}
		if (is_object($data) && ($data instanceof Expression)) {
			return $data->value();
		}
		return $this->pdo()->quote($data);
	}

	/**
	 * 开始事务
	 *
	 * @todo 事务开始时，当前请求在commit/rollback之前执行的所有Model都默认使用此数据库
	 */
	public function begin() {
		$this->pdo()->beginTransaction();
	}

	/**
	 * 提交事务
	 */
	public function commit() {
		$this->pdo()->commit();
	}

	/**
	 * 回滚事务
	 */
	public function rollback() {
		$this->pdo()->rollBack();
	}

	/**
	 * 构造新的查询对象
	 *
	 * @return Query
	 */
	public function query() {
		$query = new Query();
		$query->db($this);
		return $query;
	}

	/**
	 * 执行SQL语句
	 *
	 * @param string $sql 要执行的SQL语句
	 * @throws Exception
	 */
	public function exec($sql) {
		try {
			$this->pdo()->exec($sql);
		}
		catch (Exception $e) {//重新连接
			if (in_array($e->getCode(), [ 2006, 2013 ])) {
				$this->reconnect();
				$this->pdo()->exec($sql);
			}
			else {
				throw $e;
			}
		}
		if ($this->pdo()->errorCode() !== "00000") {
			$error = $this->pdo()->errorInfo();
			throw new Exception($error[2], $error[1]);
		}
	}

	/**
	 * 执行SQL查询语句，并返回查询到的数据
	 *
	 * @param string $sql 要执行的SQL
	 * @return array
	 */
	public function findAll($sql) {
		$stmt = $this->pdo()->query($sql);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * 执行SQL查询语句，并返回查询到的第一条数据
	 *
	 * @param string $sql 要执行的SQL
	 * @return array
	 */
	public function findOne($sql) {
		$ones = $this->findAll($sql);
		if (empty($ones)) {
			return [];
		}
		return $ones[0];
	}

	/**
	 * 执行SQL查询语句，并判断是否有记录
	 *
	 * @param string $sql 要执行的SQL
	 * @return bool
	 */
	public function exist($sql) {
		return !empty($this->findAll($sql));
	}

	/**
	 * 插入数据
	 *
	 * @param string $table 表格
	 * @param array $row 要插入的数据
	 */
	public function insert($table, array $row) {
		if (empty($row)) {
			return;
		}

		$fields = [];
		$values = [];
		foreach ($row as $name => $value) {
			if (!preg_match("/^\\w+$/", $name)) {
				continue;
			}
			$fields[] = "`" . $name . "`";
			$values[] = $this->quote($value);
		}

		$sql = "INSERT INTO `" . $table . "` (" . implode(",", $fields) . ") VALUES (" . implode(",", $values) . ")";
		$this->exec($sql);
	}

	/**
	 * 返回最后插入的数据ID
	 *
	 * 只用于自增主键
	 *
	 * @return int
	 */
	public function lastId() {
		return intval($this->pdo()->lastInsertId());
	}

	/**
	 * 取得PDO对象
	 *
	 * @return \PDO
	 */
	public function pdo() {
		if (!$this->_pdo) {
			$this->reconnect();
		}
		return $this->_pdo;
	}

	/**
	 * 列出所有数据表
	 *
	 * @return Table[]
	 */
	public function listTables() {
		$ones = $this->query()->sql("SHOW TABLES")->findAll();
		$tables = [];
		foreach ($ones as $one) {
			$tableName = current($one);
			$table = new Table();
			$table->setDb($this);
			$table->setName($tableName);
			$table->retrieveInfo();

			$tables[] = $table;
		}
		return $tables;
	}

	/**
	 * 根据表名构造数据表对象
	 *
	 * @param $tableName
	 * @return Table
	 */
	public function table($tableName) {
		$table = new Table();
		$table->setDb($this);
		$table->setName($tableName);
		$table->retrieveInfo();
		return $table;
	}

	/**
	 * 取得驱动
	 *
	 * @return drivers\MySQLDriver
	 * @TODO 需要优化
	 */
	public function driver() {
		return new drivers\MySQLDriver();
	}

	/**
	 * 重新连接
	 */
	public function reconnect() {
		$this->_pdo = new \PDO($this->_config["dsn"], $this->_config["username"], $this->_config["password"], $this->_config["options"]);
	}

	/**
	 * 关闭连接
	 */
	public function close() {
		$this->_pdo = null;

		if ($this->_dbId) {
			unset(self::$_dbs[$this->_dbId]);
		}
	}
}

?>