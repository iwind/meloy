<?php

namespace tea\db;

use tea\Arrays;
use tea\page\Page;

/**
 * 查询构造对象
 *
 * @package db
 * @todo 支持批量操作：执行、insert()、save()、delete()
 */
class Query {
	const ACTION_FIND = 1;
	const ACTION_DELETE = 2;
	const ACTION_INSERT = 3;
	const ACTION_REPLACE = 4;
	const ACTION_INSERT_OR_SAVE = 7;
	const ACTION_SAVE = 5;
	const ACTION_EXEC = 6;

	const SUB_ACTION_COUNT = 1;
	const SUB_ACTION_SUM = 2;
	const SUB_ACTION_MAX = 3;
	const SUB_ACTION_MIN = 4;
	const SUB_ACTION_AVG = 5;

	const JOIN_LEFT = 1;
	const JOIN_RIGHT = 2;

	const LOCK_SHARE_MODE = "LOCK IN SHARE MODE";
	const LOCK_FOR_UPDATE = "FOR UPDATE";

	/**
	 * @var Db
	 */
	private $_db;

	private $_stmt;

	private $_action = self::ACTION_FIND;
	private $_subAction;
	private $_table;
	private $_model;
	private $_pkName = "id";

	private $_sql;
	private $_noPk = true;
	private $_params = [];
	private $_attrs = [];
	private $_wheres = [];
	private $_havings = [];
	private $_orders = [];
	private $_groups = [];
	private $_cache = [
		"enabled" => false,
		"tags" => [],
		"key" => null,
		"life" => 0
	];
	private $_limit = -1;
	private $_offset = -1;
	private $_results = [];
	private $_joins = [];
	private $_partitions = [];
	private $_sqlCache = null;//只有查询时才用到
	private $_lock;

	private $_fields = [];//要插入或修改的字段列表
	private $_replacingFields = [];//要替换的字段列表,用于insertOrSave()


	private $_debug = false;
	private $_error = [];

	private $_filter;
	private $_map;

	private $_shouldNotify = true;

	/**
	 * 查询构造器
	 */
	public function __construct() {

	}

	/**
	 * 指定数据库
	 *
	 * @param string|Db|array $db 数据库ID、数据库对象或者数据库配置
	 * @return $this
	 */
	public function db($db) {
		if ($db instanceof Db) {
			$this->_db = $db;
		}
		else if (is_null($db)) {
			$this->_db = null;
		}
		else {
			$this->_db = Db::db($db, false);
		}
		return $this;
	}

	private function _currentDb() {
		switch ($this->_action) {
			case self::ACTION_FIND:
				return $this->_readDb();
		}
		return $this->_writeDb();
	}

	/**
	 * @return Db
	 * @throws \Exception
	 */
	private function _readDb() {
		if ($this->_db) {
			return $this->_db;
		}
		if (is_empty($this->_table)) {
			throw new \Exception("Before creating query, 'table' should be specified");
		}
		$config = o(":db");
		$configTable = $this->_table;
		if (is_array($configTable)) {
			$configTable = array_shift($configTable);
		}

		$pieces = explode(".", $configTable, 2);
		$tableName = "";
		if (count($pieces) == 1) {
			if (!isset($config["default"]["db"])) {
				throw new \Exception("':db.default.db' should be configured");
			}
			$tableName = $config["default"]["db"] . "." . $configTable;
		}
		else {
			$tableName = $configTable;
		}

		$hostConfig = [];
		if (isset($config["tables"][$tableName])) {
			$hostConfig = $config["tables"][$tableName];
		}
		else {
			list($dbName) = explode(".", $tableName, 2);
			if (isset($config["tables"][$dbName . ".*"])) {
				$hostConfig = $config["tables"][$dbName . ".*"];
			}
			else if (isset($config["tables"]["*"])) {
				$hostConfig = $config["tables"]["*"];
			}
			else {
				$defaultDb = Arrays::get($config, "default.db");
				if ($defaultDb) {
					$hostConfig = [
						"reads" => [ $defaultDb ],
						"writes" => [ $defaultDb ]
					];
				}
				else {
					if (!isset($config["default"]["db"])) {
						throw new \Exception("':db.tables.{$tableName}' should be configured");
					}
					$hostConfig = [ "reads" => [ $config["default"]["db"] ] ];
				}
			}
		}
		$hosts = [];
		if (!empty($hostConfig["reads"])) {
			$hosts = $hostConfig["reads"];
		}
		else if (!empty($hostConfig["writes"])) {
			$hosts = $hostConfig["writes"];
		}
		else {
			throw new Exception("':db.tables.{$tableName}.writes, :db.tables.{$tableName}.reads' should be configured");
		}

		$hostId = $hosts[rand(0, count($hosts) - 1)];
		$this->_db = Db::db($hostId);
		return $this->_db;
	}

	/**
	 * @return Db
	 * @throws \Exception
	 */
	private function _writeDb() {
		if ($this->_db) {
			return $this->_db;
		}
		if (is_empty($this->_table)) {
			throw new \Exception("Before creating query, 'table' should be specified");
		}
		$config = o(":db");
		$configTable = $this->_table;
		if (is_array($configTable)) {
			$configTable = array_shift($configTable);
		}
		$pieces = explode(".", $configTable, 2);
		$tableName = "";
		if (count($pieces) == 1) {
			if (!isset($config["default"]["db"])) {
				throw new \Exception("':db.default.db' should be configured");
			}
			$tableName = $config["default"]["db"] . "." . $configTable;
		}
		else {
			$tableName = $this->_table;
		}

		$hostConfig = [];
		if (isset($config["tables"][$tableName])) {
			$hostConfig = $config["tables"][$tableName];
		}
		else {
			list($dbName) = explode(".", $tableName, 2);
			if (isset($config["tables"][$dbName . ".*"])) {
				$hostConfig = $config["tables"][$dbName . ".*"];
			}
			else if (isset($config["tables"]["*"])) {
				$hostConfig = $config["tables"]["*"];
			}
			else {
				if (!isset($config["default"]["db"])) {
					throw new \Exception("':db.tables.{$tableName}' should be configured");
				}
				$hostConfig = [ "writes" => [ $config["default"]["db"]  ] ];
			}
		}
		$hosts = [];
		if (!empty($hostConfig["writes"])) {
			$hosts = $hostConfig["writes"];
		}
		else {
			throw new \Exception("':db.tables.{$tableName}.writes' should be configured");
		}

		$hostId = $hosts[rand(0, count($hosts) - 1)];
		$this->_db = Db::db($hostId);

		return $this->_db;
	}

	/**
	 * 设置数据表
	 *
	 * @param string $table 数据表
	 * @return $this 当前对象
	 */
	public function table($table) {
		$this->_table = $table;
		return $this;
	}

	/**
	 * 设置表对应的模型
	 *
	 * @param string $model 模型类，包含完成的命名空间
	 * @return $this
	 */
	public function model($model) {
		$this->_model = $model;
		return $this;
	}

	/**
	 * 设置主键名
	 *
	 * @param string $pkName 主键名
	 * @return $this
	 * @TODO 支持联合主键
	 */
	public function pkName($pkName) {
		$this->_pkName = $pkName;
		return $this;
	}

	/**
	 * 设置是否返回主键
	 *
	 * 默认find或findAll查询中返回主键，以便后续可以用此主键值操作对象
	 *
	 * @param bool $noPk 是否返回主键
	 * @return $this
	 */
	public function noPk($noPk = true) {
		$this->_noPk = $noPk;
		return $this;
	}

	/**
	 * 设置状态查询
	 *
	 * 相当于：attr("state", $state)
	 *
	 * @param int|array $state 状态值
	 * @return $this
	 */
	public function state($state) {
		if (is_array($state)) {
			$this->attr("state", $state);
		}
		else {
			$this->attr("state", intval($state));
		}
		return $this;
	}

	/**
	 * 指定SQL语句
	 *
	 * @param string $sql SQL语句
	 * @return $this
	 */
	public function sql($sql) {
		$this->_sql = $sql;
		return $this;
	}

	/**
	 * 设定查询语句中的参数值
	 *
	 * 只有指定where和sql后，才能使用该方法
	 *
	 * @param string $name 参数名
	 * @param mixed $value 参数值
	 * @return $this
	 */
	public function param($name, $value) {
		$this->_params[$name] = $value;
		return $this;
	}

	/**
	 * 设置字段值，以便用于删除和修改操作
	 *
	 * @param string $name 字段名
	 * @param mixed $value 属性值
	 * @return $this
	 */
	public function set($name, $value) {
		$this->_fields[$name] = $value;
		return $this;
	}

	/**
	 * 设置一组字段值，以便用于删除和修改操作
	 *
	 * @param array $fields 字段名和字段值键值对
	 * @return $this
	 */
	public function sets(array $fields) {
		$this->_fields = array_merge($this->_fields, $fields);
		return $this;
	}

	/**
	 * 增加某个字段的数值
	 *
	 * @param string $field 字段名
	 * @param int $count 增加的数量
	 * @return $this
	 * @throws \Exception
	 */
	public function increase($field, $count = 1) {
		if (is_object($count)) {
			if ($count instanceof Expression) {
				$count = $count->value();
			}
			else if ($count instanceof Query) {
				$count = "(" . $count->asSql() . ")";
 			}
		}
		$this->_fields[] = $field . "=" . $field . "+{$count}";
		return $this;
	}

	/**
	 * 减少某个字段的数值
	 *
	 * @param string $field 字段名
	 * @param int $count 减少的数量
	 * @return $this
	 * @throws \Exception
	 */
	public function decrease($field, $count = 1) {
		if (is_object($count)) {
			if ($count instanceof Expression) {
				$count = $count->value();
			}
			else if ($count instanceof Query) {
				$count = "(" . $count->asSql() . ")";
			}
		}
		$this->_fields[] = $field . "=" . $field . "-{$count}";
		return $this;
	}

	/**
	 * 设置查询的字段
	 *
	 * @param string $name 字段名
	 * @param mixed $value 字段值
	 * @return $this
	 */
	public function attr($name, $value) {
		$this->_attrs[$name] = $value;
		return $this;
	}

	/**
	 * 设置一组查询的字段
	 *
	 * @param array $attrs 一组字段名和字段值的键值对
	 * @return $this
	 */
	public function attrs(array $attrs) {
		$this->_attrs = array_merge($this->_attrs, $attrs);
		return $this;
	}

	/**
	 * 设置where条件
	 *
	 * @param mixed $where 查询条件
	 * @param mixed $appends 可以连接的条件
	 * @return $this
	 */
	public function where($where, ... $appends) {
		$prefix = "";
		$suffix = "";
		if (!empty($appends)) {
			$prefix = "(";
		}
		if (is_object($where)) {
			if ($where instanceof Expression) {
				$where = $where->value();
			}
			else if ($where instanceof Query) {
				$where = $where->sqlCache(null)->asSql();
			}
		}
		if (!empty($appends)) {
			foreach ($appends as $append) {
				if (is_object($append)) {
					if ($append instanceof Expression) {
						$append = $append->value();
					}
					else if ($append instanceof Query) {
						$append = "(" . $append->sqlCache(null)->asSql() . ")";
					}
				}
				$where .= $append;
			}

			$suffix = ")";
		}
		$this->_wheres[] = $prefix . $where . $suffix;
		return $this;
	}

	/**
	 * 设置between条件
	 *
	 * @param mixed $value 要判断的值
	 * @param mixed $min 最小值
	 * @param mixed $max 最大值
	 * @return $this
	 */
	public function between($value, $min, $max) {
		if ($value instanceof Expression) {
			$value = $this->_quoteKeyword($value->value());
		}
		else {
			$value = $this->_quoteValue($value);
		}

		if ($min instanceof Expression) {
			$min = $this->_quoteKeyword($min->value());
		}
		else {
			$min = $this->_quoteValue($min);
		}

		if ($max instanceof Expression) {
			$max = $this->_quoteKeyword($max->value());
		}
		else {
			$max = $this->_quoteValue($max);
		}

		$this->_wheres[] = $value . " BETWEEN {$min} AND {$max}";

		return $this;
	}

	/**
	 * 设置要查询的主键值
	 *
	 * @param mixed $pks 一组主键值
	 * @return $this
	 */
	public function pk(... $pks) {
		$realPks = [];
		foreach ($pks as $pk) {
			if (is_array($pk)) {
				$realPks = array_merge($realPks, $pk);
			}
			else {
				$realPks[] = $pk;
			}
		}
		$this->attr($this->_pkName, $realPks);
		return $this;
	}

	/**
	 * 指定分区
	 *
	 * @param string|null $partitions 分区名，比如p1, p2
	 * @return $this
	 */
	public function partitions(... $partitions) {
		foreach ($partitions as $partition) {
			if (is_array($partition)) {
				$this->_partitions = array_merge($this->_partitions, $partition);
			}
			else {
				$this->_partitions[] = $partition;
			}
		}
		return $this;
	}

	/**
	 * 行锁定
	 *
	 * @param string $type 类型，请参看Query::LOCK_*
	 * @return $this
	 */
	public function lock($type = self::LOCK_FOR_UPDATE) {
		$this->_lock = $type;
		return $this;
	}

	/**
	 * 是否开启SQL Cache
	 *
	 * 默认为True，只有在SELECT时才有作用
	 *
	 * @param bool|null $bool 是否开启，如果为null，则不加入SQL CACHE设置
	 * @return $this
	 */
	public function sqlCache($bool = true) {
		$this->_sqlCache = $bool;
		return $this;
	}

	/**
	 * 设置like查询条件
	 *
	 * 对表达式自动加上百分号， % ... %
	 *
	 * @param string $attr 字段名
	 * @param string $expr Like表达式
	 * @return $this
	 */
	public function like($attr, $expr) {
		$this->_wheres[] = $attr . " LIKE '%" . trim($this->_quoteValue($expr), "'") . "%'";
		return $this;
	}

	/**
	 * 设置Group查询条件
	 *
	 * @param string $group 用于Group的字段名
	 * @param string|null $order 排序
	 * @return $this
	 */
	public function group($group, $order = null) {
		$this->_groups[] = [ $group, $order ];
		return $this;
	}

	/**
	 * 设置Having条件
	 *
	 * @param string $cond 条件
	 * @return $this
	 */
	public function having($cond) {
		$this->_havings[] = $cond;
		return $this;
	}

	/**
	 * 设置正序
	 *
	 * @param string|null $field 字段名
	 * @return $this
	 */
	public function asc($field = null) {
		$this->_orders[] = array(
			"field" => $field,
			"type" => "asc"
		);
		return $this;
	}

	/**
	 * 设置倒序
	 *
	 * @param string|null $field 字段名
	 * @return $this
	 */
	public function desc($field = null) {
		if (is_null($field)) {
			$field = $this->_pkName;
		}
		$this->_orders[] = array(
			"field" => $field,
			"type" => "desc"
		);
		return $this;
	}

	/**
	 * 设置一组排序条件
	 *
	 * @param string $orders 排序条件
	 * @return $this
	 */
	public function order($orders) {
		$this->_orders[] = array(
			"field" => $orders,
			"type" => "order"
		);
		return $this;
	}

	/**
	 * 设置一组联合查询条件
	 *
	 * @param array $joins 联合查询条件
	 * @return $this
	 */
	public function joins(array $joins) {
		$this->_joins = $joins;
		return $this;
	}

	/**
	 * 判断是否有联合查询条件
	 *
	 * @return bool
	 */
	public function hasJoins() {
		return !empty($this->_joins);
	}

	/**
	 * 设置单个联合查询条件
	 *
	 * @param string $model 模型类
	 * @param string|null $type 见self::TYPE_LEFT, self::TYPE_RIGHT
	 * @param string|null $on 联合查询条件
	 * @return $this
	 */
	public function join($model, $type = null, $on = null) {
		//是否已经有同种类型的连接
		$hasJoined = false;
		foreach ($this->_joins as &$join) {
			if ($join["model"] == $model) {
				$join = [
					"model" => $model,
					"type" => $type,
					"on" => $on
				];
				$hasJoined = true;
				break;
			}
		}

		if (!$hasJoined) {
			$this->_joins[] = [
				"model" => $model,
				"type" => $type,
				"on" => $on
			];
		}
		return $this;
	}

	/**
	 * 设置Limit条件，同size()
	 *
	 * @param int $limit 要返回记录条数
	 * @return $this
	 */
	public function limit($limit) {
		$this->_limit = $limit;
		return $this;
	}

	/**
	 * 设置Limit条件，同limit()
	 *
	 * @param int $size 要返回记录条数
	 * @return $this
	 */
	public function size($size) {
		return $this->limit($size);
	}

	/**
	 * 设置偏移量
	 *
	 * @param int $offset 偏移量
	 * @return $this
	 */
	public function offset($offset) {
		$this->_offset = $offset;
		return $this;
	}

	/**
	 * 设置分页
	 *
	 * 可利用分页快速设置offset和limit参数
	 *
	 * @param \tea\page\Page $page 分页对象
	 * @return $this
	 */
	public function page(Page $page) {
		$this->_limit = $page->size();
		$this->_offset = $page->offset();
		return $this;
	}

	/**
	 * 设置查询要返回的字段
	 *
	 * - 字段名中支持星号(*)通配符
	 *
	 * @param array $fields 要返回的字段
	 * @return $this
	 */
	public function result(... $fields) {
		foreach ($fields as $field) {
			if (is_string($field)) {
				$this->_results[] = $field;
			}
			else if (is_array($field)) {
				$this->_results = array_merge($this->_results, $field);
			}
			else if (is_object($field)) {
				$this->_results[] = $field;
			}
		}
		return $this;
	}

	/**
	 * 设置返回主键字段值
	 *
	 * @return $this
	 */
	public function resultPk() {
		return $this->result($this->_pkName);
	}

	/**
	 * 设置是否开启调试模式
	 *
	 * 如果开启调试模式，会打印SQL语句
	 *
	 * @param bool $debug 是否开启调试模式
	 * @return $this
	 */
	public function debug($debug = true) {
		$this->_debug = $debug;

		return $this;
	}

	/**
	 * 设置过滤函数
	 *
	 * @param callable $callback 过滤函数
	 * @return $this
	 */
	public function filter(callable $callback) {
		if ($callback) {
			$this->_filter = function ($value) use ($callback) {
				$ret = call_user_func($callback, $value);
				return ($ret !== false);
			};
		}
		else {
			$this->_filter = null;
		}
		return $this;
	}

	/**
	 * 设置map函数
	 *
	 * @param callable|int|string $callback map函数, 如果是int表示取第N个字段, 如果是string表示取字段名对应的值
	 * @return $this
	 */
	public function map($callback) {
		if (is_int($callback)) {
			$index = $callback;
			$this->_map = function ($row) use ($index) {
				$values = array_values(normalize($row, false));
				$i = 0;
				foreach ($values as $index2 => $value) {
					if (!is_null($value)) {
						if ($i == $index) {
							return $value;
						}

						$i ++;
					}
				}
				return null;
			};
		}
		else if (is_string($callback)) {
			$key = $callback;
			$this->_map = function ($row) use ($key) {
				$values = normalize($row, false);
				foreach ($values as $key2 => $value) {
					if ($key == $key2) {
						return $value;
					}
				}
				return null;
			};
		}
		else if (is_callable($callback)) {
			$this->_map = $callback;
		}
		else {
			$this->_map = null;
		}
		return $this;
	}

	/**
	 * 在对对象进行创建、修改、删除操作时是否通知模型中的事件
	 *
	 * 此选项打开时，如果有对象修改操作而且参数中有主键值时自动调用模型的onBeforeXxx，onAfterXxx...之类的事件
	 *
	 * @param bool $shouldNotify 是否通知
	 * @return $this
	 */
	public function notify($shouldNotify = true) {
		$this->_shouldNotify = $shouldNotify;

		return $this;
	}

	/**
	 * 设置当前查询要执行的动作
	 *
	 * @param string $action 见self::ACTION_*
	 * @return $this
	 */
	public function action($action) {
		$this->_action = $action;
		return $this;
	}

	/**
	 * 设置当前查询要执行的子动作
	 *
	 * @param string $subAction 见self::SUB_ACTION_*
	 * @return $this
	 */
	public function subAction($subAction) {
		$this->_subAction = $subAction;
		return $this;
	}

	private function _partitionsSQL() {
		if (!empty($this->_partitions)) {
			return " PARTITION(" . implode(", ", $this->_partitions) . ") ";
		}
		return "";
	}

	/**
	 * 将查询转换为SQL语句
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function asSql() {
		$sql = $this->_sql;

		if (is_null($this->_sql)) {
			if ( is_null($this->_table)) {
				throw new \Exception("You need specify a table name");
			}

			if ($this->_action == self::ACTION_FIND) {
				$resultString = "*";
				if(!empty($this->_results)) {
					if (!$this->_noPk && !is_null($this->_pkName) && !in_array($this->_pkName, $this->_results)) {
						$this->_results[] = $this->_quoteKeyword($this->_pkName);
					}
					$results = $this->_results;
					$newResults = [];
					foreach ($results as &$result) {
						if (is_object($result)) {
							if ($result instanceof Expression) {
								$newResults[] = $result->value();
							}
							else if ($result instanceof Query) {
								$newResults[] = "(" . $result->sqlCache(null)->asSql() . ")";
							}
						}
						else {
							if ($this->_model != null) {//支持星号(*)通配符
								if (trim($result) != "*" && preg_match("/^(\\w*)\\*(\\w*)$/", $result, $match)) {
									$pattern = $match[1] . ".*" . $match[2];
									$modelFields = get_object_vars(new $this->_model);
									foreach ($modelFields as $modelField => $_v) {
										if (preg_match("/^" . $pattern . "$/", $modelField)) {
											$newResults[] = $this->_quoteKeyword($modelField);
										}
									}
									continue;
								}
							}
							$newResults[] = $this->_quoteKeyword($result);
						}
					}
					$resultString = implode(", ", $newResults);
				}
				$sql = "SELECT";
				if ($this->_sqlCache) {
					$sql .= " SQL_CACHE ";
				}
				else if (!is_null($this->_sqlCache)) {
					$sql .= " SQL_NO_CACHE ";
				}
				$sql .= "\n {$resultString}\n FROM ";
				if (is_array($this->_table)) {
					$tableStrings = [];
					foreach ($this->_table as $abbr => $table) {
						$tableStrings[] = $this->_quoteTable($table) . " AS " . $abbr;
					}
					$sql .= implode(", ", $tableStrings);
				}
				else {
					$sql .= $this->_quoteTable($this->_table);
				}

				//JOIN
				if (!empty($this->_joins)) {
					$hasOtherJoins = false;
					foreach ($this->_joins as $join) {
						$model = $join["model"];
						if (isset($join["type"]) && $join["type"]) {
							$hasOtherJoins = true;
						}
						else {
							if ($model::db() == $this->_db->id()) {
								$sql .= ", " . $model::table();
							}
							else {
								$anotherDb = Db::db($model::db());
								$sql .= ", " . $anotherDb->name(). "." . $model::table();
							}
						}
					}

					if ($hasOtherJoins) {
						foreach ($this->_joins as $join) {
							$model = $join["model"];
							if (isset($join["type"]) && $join["type"] == self::JOIN_LEFT) {
								if ($model::db() == $this->_db->id()) {
									$sql .= "\n LEFT JOIN " . $model::table();
								}
								else{
									$anotherDb = Db::db($model::db());
									$sql .= "\n LEFT JOIN " . $anotherDb->name() . "." . $model::table();
								}

								$sql .= $this->_partitionsSQL();

								if (isset($join["on"]) && $join["on"]) {
									$sql .= " ON " . $join["on"];
								}
							}
							else if (isset($join["type"]) && $join["type"] == self::JOIN_RIGHT) {
								if ($model::db() == $this->_db->id()) {
									$sql .= "\n RIGHT JOIN " . $model::table();
								}
								else {
									$anotherDb = Db::db($model::db());
									$sql .= "\n RIGHT JOIN " . $anotherDb->name() . "." . $model::table();
								}

								$sql .= $this->_partitionsSQL();

								if (isset($join["on"]) && $join["on"]) {
									$sql .= " ON " . $join["on"];
								}
							}
						}
					}
					else {
						$sql .= $this->_partitionsSQL();
					}
				}
				else {
					$sql .= $this->_partitionsSQL();
				}
			}
			else if ($this->_action == self::ACTION_DELETE) {
				$sql = "DELETE FROM " . $this->_quoteTable($this->_table);
				$sql .= $this->_partitionsSQL();
			}
			else if ($this->_action == self::ACTION_SAVE) {
				$sql = "UPDATE {$this->_table}\n " . $this->_partitionsSQL() . "SET";

				$fieldStrings = [];
				if (!empty($this->_fields)) {
					foreach ($this->_fields as $field => $value) {
						if (is_int($field)) {
							$fieldStrings[] = $this->_quoteKeyword($value);
						}
						else {
							$fieldStrings[] = $this->_quoteKeyword($field) . "=" . $this->_currentDb()->quote($value);
						}
					}
					$sql .= " " . implode(", ", $fieldStrings);
				}
			}
			else if ($this->_action == self::ACTION_INSERT) {
				$sql = "INSERT " . $this->_quoteTable($this->_table) . "\n ";
				$sql .= $this->_partitionsSQL();

				$fieldStrings = [];
				if (!empty($this->_fields)) {
					$fieldNames = [];
					$fieldValues = [];
					foreach ($this->_fields as $field => $value) {
						$fieldNames[] = $this->_quoteKeyword($field);
						$fieldValues[] = $this->_currentDb()->quote($value);
					}
					$sql .= " (" . implode(", ", $fieldNames) . ") VALUES (" . implode(", ", $fieldValues) . ")";
				}
			}
			else if ($this->_action == self::ACTION_REPLACE) {
				$sql = "REPLACE " . $this->_quoteTable($this->_table) . "\n ";
				$sql .= $this->_partitionsSQL();

				$fieldStrings = [];
				if (!empty($this->_fields)) {
					$fieldNames = [];
					$fieldValues = [];
					foreach ($this->_fields as $field => $value) {
						$fieldNames[] = $this->_quoteKeyword($field);
						$fieldValues[] = $this->_currentDb()->quote($value);
					}
					$sql .= " (" . implode(", ", $fieldNames) . ") VALUES (" . implode(", ", $fieldValues) . ")";
				}
			}
			else if ($this->_action == self::ACTION_INSERT_OR_SAVE) {
				$sql = "INSERT " . $this->_quoteTable($this->_table) . "\n ";
				$sql .= $this->_partitionsSQL();

				$fieldStrings = [];
				if (!empty($this->_fields)) {
					$fieldNames = [];
					$fieldValues = [];
					foreach ($this->_fields as $field => $value) {
						$fieldNames[] = $this->_quoteKeyword($field);
						$fieldValues[] = $this->_currentDb()->quote($value);
					}
					$sql .= " (" . implode(", ", $fieldNames) . ") VALUES (" . implode(", ", $fieldValues) . ")";
				}
				$sql .= "\nON DUPLICATE KEY UPDATE\n";

				$fieldStrings = [];
				if (!empty($this->_replacingFields)) {
					foreach ($this->_replacingFields as $field => $value) {
						if (is_int($field)) {
							$fieldStrings[] = $this->_quoteKeyword($value);
						}
						else {
							$fieldStrings[] = $this->_quoteKeyword($field) . "=" . $this->_currentDb()->quote($value);
						}
					}
					$sql .= "  " . implode(", ", $fieldStrings);
				}
			}
		}
		else {
			$sql = $this->_sql;

			//@todo 支持$this->_results
		}

		//attrs
		$wheres = [];
		if (!empty($this->_attrs)) {
			foreach ($this->_attrs as $attr => $value) {
				if (is_array($value)) {
					if (empty($value)) {
						continue;
					}
					if (!empty($this->_joins)) {
						$attr = $this->_quoteKeyword($attr);
					}
					else {
						$attr = $this->_quoteKeyword($attr);
					}
					$ins = [];
					foreach ($value as $_value) {
						$ins[] = $this->_currentDb()->quote($_value);
					}
					if (count($ins) == 1) {
						$wheres[] = $attr . "=" . $ins[0];
					}
					else {
						$wheres[] = $attr . " IN (" . implode(", ", $ins) . ")";
					}
				}
				else if (is_object($value)) {
					if ($value instanceof Expression) {
						$value = $value->value();
						$wheres[] = $attr . "=" . $value;
					}
					else if ($value instanceof Query) {
						$wheres[] = $attr . "=(" . $value->asSql() . ")";
					}
				}
				else {
					if (!empty($this->_joins)) {
						$attr = $this->_quoteKeyword($attr);
					}
					else {
						$attr = $this->_quoteKeyword($attr);
					}
					$wheres[] = $attr . "=" . $this->_currentDb()->quote($value);
				}
			}
		}

		//where
		$wheres = array_merge($wheres, $this->_wheres);
		if ($this->_action != self::ACTION_INSERT && !empty($wheres)) {
			$whereString = implode(" AND ", $wheres);

			//@TODO 判断where是否已存在
			$sql .= "\n WHERE " . $whereString;
		}

		//Having
		if (!empty($this->_havings)) {
			$sql .= "\n HAVING " . implode(" AND ", $this->_havings);
		}

		//group
		if ($this->_action == self::ACTION_FIND && !empty($this->_groups)) {
			foreach ($this->_groups as $group) {
				$sql .= "\n GROUP BY {$group[0]} {$group[1]}";
			}
		}

		//orders
		if (!empty($this->_orders)) {
			$orderStrings = [];
			foreach($this->_orders as $order) {
				$field = $order["field"];
				$type = $order["type"];

				if ($field == null) {
					$field = $this->_pkName;
				}

				if ($type == "order") {
					$orderStrings[] = $this->_quoteKeyword($field);
				}
				else if ($type == "asc") {
					$orderStrings[] = $this->_quoteKeyword($field) . " ASC";
				}
				else if ($type == "desc") {
					$orderStrings[] = $this->_quoteKeyword($field) . " DESC";
				}
			}
			$orderString = implode(", ", $orderStrings);

			//@TODO 支持已有order by的补充
			//@TODO 判断order by是否在limit, having, group之前
			$sql .= "\n ORDER BY {$orderString}";
		}

		//limit & offset
		if (!$this->_subAction) {
			if($this->_limit > -1) {
				if($this->_offset > -1) {
					$sql .= "\n LIMIT {$this->_offset},{$this->_limit}";
				}
				else {
					$sql .= "\n LIMIT {$this->_limit}";
				}
			}
		}

		//JOIN
		if (!empty($this->_joins)) {
			$sql = preg_replace("/\\bself\\b/", $this->_table, $sql);
			foreach ($this->_joins as $join) {
				$model = $join["model"];
				$pos = strrpos($model, "\\");
				if($pos === false) {
					$className = $model;
				}
				else {
					$className = substr($model, $pos + 1);
				}
				$sql = preg_replace("/\\b" . $className . "\\b/", $model::table(), $sql);
			}
		}

		//锁定
		if ($this->_action == self::ACTION_FIND) {
			if ($this->_lock) {
				$sql .= "\n " . $this->_lock;
			}
		}

		if ($this->_debug) {
			p("[DEBUG]" . $sql . "\n");
		}

		return $sql;
	}

	/**
	 * 查询数据，返回数组
	 *
	 * @param mixed $pk 主键值
	 * @return array
	 * @throws Exception
	 */
	public function findOne($pk = null) {
		if (is_numeric($pk)) {
			$this->pk($pk);
		}

		$this->_limit = 1;
		if ($this->_offset < 0) {
			$this->_offset = 0;
		}
		$ones = $this->_findAll(true);
		if (!empty($ones)) {
			return $ones[0];
		}
		return [];
	}

	/**
	 * 查询数据，返回模型对象
	 *
	 * @param mixed $pk 主键值
	 * @return mixed
	 * @throws Exception
	 */
	public function find($pk = null) {
		if (is_numeric($pk) || is_string($pk)) {
			$this->pk($pk);
		}

		$this->_limit = 1;
		if ($this->_offset < 0) {
			$this->_offset = 0;
		}
		$ones = $this->_findAll(false);

		if (!empty($ones)) {
			return $ones[0];
		}
		return null;
	}

	/**
	 * 根据某个请求参数值进行查询
	 *
	 * @param string $paramName 请求参数（通过GET或POST传递）
	 * @return null|Model
	 */
	public function findx($paramName = "id") {
		$value = x($paramName);
		if (is_null($value)) {
			return null;
		}
		return $this->find($value);
	}

	/**
	 * 查询一组数据
	 *
	 * @param bool|false $returnArray 数组中每一条是否用数组表示；如果false则以模型对象返回
	 * @return array
	 * @throws Exception
	 */
	public function findAll($returnArray = false) {
		return $this->_findAll($returnArray);
	}

	private function _findAll($returnArray = true) {
		$this->_action = self::ACTION_FIND;
		$sql = null;

		//cache
		$cache = false;
		$cacheKey = null;
		if ($this->_cache["enabled"]) {
			$cacheService = \pp\cache\Service::service();
			if ($cacheService->isEnabled()) {
				$cache = true;

				if ($this->_cache["key"]) {
					$cacheKey = $this->_cache["key"];
				}
				else {
					$sql = $this->asSql();
					$cacheKey = md5($sql . serialize($this->_params));
				}

				$value = $cacheService->get($cacheKey);
				if (!is_empty($value)) {
					if ($this->_debug) {
						p("fetch data from cache '{$cacheKey}'");
					}
					return unserialize($value);
				}
			}
		}
		if (is_null($sql)) {
			$sql = $this->asSql();
		}

		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		$bool = $stmt->execute();

		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		$ones = $stmt->fetchAll(\PDO::FETCH_NAMED);

		if (!empty($ones)) {
			//数据类型
			$dataTypes = [];
			$columnsCount = $stmt->columnCount();
			for ($i = 0; $i < $columnsCount; $i ++) {
				$dataTypes[] = $stmt->getColumnMeta($i)["native_type"];
			}
			foreach ($ones as &$one) {
				$i = 0;
				foreach ($one as &$value) {
					$type = $dataTypes[$i];
					switch ($type) {
						case "LONG":
						case "LONGLONG":
						case "INTEGER":
						case "TINY":
						case "SHORT":
						case "INT24":
						case "YEAR":
							$value = intval($value);
							break;
						case "FLOAT":
						case "DECIMAL":
						case "NEWDECIMAL":
							$value = floatval($value);
							break;
						case "DOUBLE":
							$value = doubleval($value);
							break;
					}

					$i ++;
				}
			}

			//Model
			if (!$returnArray && $this->_model) {
				array_walk($ones, function (&$value) {
					$value = $this->_arrayToModel($value);
				});
			}

			//Each
			if ($this->_filter) {
				$ones = array_values(array_filter($ones, $this->_filter));
			}
			if ($this->_map) {
				$ones = array_map($this->_map, $ones);
			}
		}

		//缓存
		if ($cache) {
			$life = isset($this->_cache["life"]) ? $this->_cache["life"] : 0;
			$cacheService = \pp\cache\Service::service();
			$cacheService->set($cacheKey, serialize($ones), $life);
		}

		return $ones;
	}

	/**
	 * 查询单个字段值
	 *
	 * @param mixed $default 默认值
	 * @param int $colIndex 字段位置
	 * @return mixed
	 */
	public function findCol($default = null, $colIndex = 0) {
		$this->_limit = 1;
		$this->_offset = 0;
		$this->noPk();
		$one = array_values($this->findOne());
		if (isset($one[$colIndex])) {
			return $one[$colIndex];
		}
		return $default;
	}

	/**
	 * 执行COUNT查询
	 *
	 * @param mixed $attr 字段名
	 * @return int
	 */
	public function count($attr = null) {
		if (is_null($attr)) {
			$attr = "*";
		}
		$this->_action = self::ACTION_FIND;
		$this->_subAction = self::SUB_ACTION_COUNT;
		$this->noPk();
		$this->_results = [ "COUNT(" . $attr . ")" ];
		return intval($this->findCol(0));
	}

	/**
	 * 执行SUM查询
	 *
	 * @param mixed $attr 字段名
	 * @param float $default 默认值
	 * @return float
	 */
	public function sum($attr, $default = 0) {
		$this->_action = self::ACTION_FIND;
		$this->_subAction = self::SUB_ACTION_SUM;
		$this->_results = [ "SUM(" . $attr . ")" ];
		$this->noPk();
		return floatval($this->findCol($default));
	}

	/**
	 * 执行MIN查询
	 *
	 * @param mixed $attr 字段名
	 * @return int
	 */
	public function min($attr, $default = null) {
		$this->_action = self::ACTION_FIND;
		$this->_subAction = self::SUB_ACTION_MIN;
		$this->_results = [ "MIN(" . $attr . ")" ];
		$this->noPk();
		return floatval($this->findCol($default));
	}

	/**
	 * 执行MAX查询
	 *
	 * @param mixed $attr 字段名
	 * @return int
	 */
	public function max($attr, $default = null) {
		$this->_action = self::ACTION_FIND;
		$this->_subAction = self::SUB_ACTION_MAX;
		$this->_results = [ "MAX(" . $attr . ")" ];
		$this->noPk();
		return floatval($this->findCol($default));
	}

	/**
	 * 执行AVG查询
	 *
	 * @param mixed $attr 字段名
	 * @return int
	 */
	public function avg($attr, $decimal = 0, $default = 0) {
		$this->_action = self::ACTION_FIND;
		$this->_subAction = self::SUB_ACTION_AVG;
		$this->_results = [ "AVG(" . $attr . ")" ];
		$this->noPk();
		return floatval($this->findCol($default));
	}

	/**
	 * 判断记录是否存在
	 *
	 * @return bool
	 */
	public function exist() {
		return !empty(self::resultPk()->findOne());
	}

	/**
	 * 执行查询
	 *
	 * @return bool 是否执行成功
	 * @throws \Exception
	 */
	public function exec() {
		$this->_action = self::ACTION_EXEC;
		$sql = $this->asSql();
		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		$bool = $stmt->execute();
		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		return $bool;
	}

	/**
	 * 执行REPLACE
	 *
	 * @param array $fields 一组字段值
	 * @return bool
	 * @throws \Exception
	 */
	public function replace(array $fields = []) {
		$this->_action = self::ACTION_REPLACE;

		$this->_fields = array_merge($this->_fields, $fields);

		$sql = $this->asSql();
		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		$bool = $stmt->execute();
		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		return $bool;
	}

	/**
	 * 执行INSERT
	 *
	 * @param array $fields 一组字段值
	 * @return bool
	 * @throws \Exception
	 */
	public function insert(array $fields = []) {
		$this->_action = self::ACTION_INSERT;

		$this->_fields = array_merge($this->_fields, $fields);

		$sql = $this->asSql();
		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		//触发事件
		if ($this->_shouldNotify && $this->_model != null) {
			/** @var Model $object */
			$object = new $this->_model();
			$object->onBeforeCreate();
			$object->onBeforeCreateAndUpdate();
		}

		$bool = $stmt->execute();
		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		//触发事件
		if ($this->_shouldNotify && $this->_model != null) {
			/** @var Model $object */
			$object = new $this->_model([
				$this->_pkName => $this->lastId()
			]);
			$object->onAfterCreateAndUpdate();
			$object->onAfterCreate();
		}

		return $bool;
	}

	/**
	 * @return Model[]
	 */
	private function _relatedObjects() {
		$objects = [];

		//事件
		if ($this->_shouldNotify && $this->_model != null && !empty($this->_attrs[$this->_pkName])) {
			$pks = $this->_attrs[$this->_pkName];
			if (is_scalar($pks)) {
				$objects[] = new $this->_model([
					$this->_pkName => $pks
				]);
			}
			else {
				foreach ($pks as $pk) {
					if (is_scalar($pk)) {
						$objects[] = new $this->_model([
							$this->_pkName => $pk
						]);
					}
				}
			}
		}

		return $objects;
	}

	/**
	 * 执行UPDATE
	 *
	 * @param array $fields 一组字段值
	 * @return bool
	 * @throws \Exception
	 */
	public function save(array $fields = []) {
		$this->_fields = array_merge($this->_fields, $fields);

		$this->_action = self::ACTION_SAVE;
		$sql = $this->asSql();
		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		//触发事件
		$objects = $this->_relatedObjects();
		if (!empty($objects)) {
			array_walk($objects, function (Model $v) {
				$v->onBeforeUpdate();
				$v->onBeforeCreateAndUpdate();
			});
		}
		else {
			$object = new $this->_model;
			$object->onBeforeUpdate();
			$object->onBeforeCreateAndUpdate();
		}

		$bool = $stmt->execute();
		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		if (!empty($objects)) {
			array_walk($objects, function (Model $v) {
				$v->onAfterCreateAndUpdate();
				$v->onAfterUpdate();
			});
		}
		else {
			$object = new $this->_model;
			$object->onAfterCreateAndUpdate();
			$object->onAfterUpdate();
		}

		return $bool;
	}

	/**
	 * 执行DELETE
	 *
	 * @return int 删除的条数
	 * @throws \Exception
	 */
	public function delete() {
		$this->_action = self::ACTION_DELETE;
		$sql = $this->asSql();
		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		//触发事件
		$objects = $this->_relatedObjects();
		if (!empty($objects)) {
			array_walk($objects, function (Model $v) {
				$v->onBeforeDelete();
			});
		}
		else {
			$object = new $this->_model;
			$object->onBeforeDelete();
		}

		$bool = $stmt->execute();
		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		if (!empty($objects)) {
			array_walk($objects, function (Model $v) {
				$v->onAfterDelete();
			});
		}
		else {
			$object = new $this->_model;
			$object->onAfterDelete();
		}

		return $stmt->rowCount();
	}

	/**
	 * 插入或更改
	 *
	 * 依据要插入的数据中的unique键来决定是插入数据还是替换数据
	 *
	 * @param array $insertingFields 要插入的字段列表
	 * @param array $replacingFields 要替换的字段列表
	 * @return int 影响的行数
	 */
	public function insertOrSave(array $insertingFields, array $replacingFields) {
		$this->_fields = $insertingFields;
		$this->_replacingFields = $replacingFields;

		$this->_action = self::ACTION_INSERT_OR_SAVE;
		$sql = $this->asSql();

		$stmt = $this->_currentDb()->pdo()->prepare($sql);

		//params
		if (!empty($this->_params)) {
			foreach ($this->_params as $param => $value) {
				$stmt->bindValue(":" . $param, $value);
			}
		}

		$bool = $stmt->execute();
		if (!$bool) {
			$this->_parseError($stmt, $sql);
		}

		return $stmt->rowCount();
	}

	/**
	 * 取得最后插入的数据ID
	 *
	 * @return int
	 */
	public function lastId() {
		return $this->_currentDb()->lastId();
	}

	/**
	 * 设置缓存时间
	 *
	 * @param int $life 缓存时间
	 * @return $this
	 */
	public function life($life = 86400) {
		$this->_cache["life"] = $life;
		return $this;
	}

	/**
	 * 设置缓存
	 *
	 * @param string $cacheKey 键值
	 * @param array $params 键值中的参数
	 * @return $this
	 */
	public function cache($cacheKey, $params = []) {
		$this->_cache["enabled"] = true;

		if (!empty($params)) {
			if (is_array($params)) {
				foreach ($params as $key => $value) {
					$cacheKey = str_replace('%{' . $key . '}', $value, $cacheKey);
				}
			}
			else {
				$cacheKey = str_replace('%{' . $this->_pkName . '}', $params, $cacheKey);
			}
		}

		//加上模型的相关标识
		if ($this->_model != null) {
			$class = $this->_model;
			$cacheKey = $this->_model . "@" . $class::$VERSION . "@" . $cacheKey;
		}

		$this->_cache["key"] = $cacheKey;

		return $this;
	}

	/**
	 * 清除缓存
	 */
	public function purge() {
		if (!$this->_cache["enabled"] || is_empty($this->_cache["key"])) {
			return;
		}

		\pp\cache\Service::service()->delete($this->_cache["key"]);
	}

	/**
	 * 取得最后一条语句执行后的错误信息
	 *
	 * @return array
	 */
	public function error() {
		return $this->_error;
	}

	private function _arrayToModel($array) {
		if (!$this->_model) {
			throw new Exception("No model was defined");
		}
		$model = new $this->_model($array);
		return $model;
	}

	private function _quoteTable($keyword) {
		if (preg_match("/^\\w+$/", $keyword)) {
			return "`" . $keyword . "`";
		}
		return $keyword;
	}

	private function _quoteKeyword($keyword) {
		if (preg_match("/^\\w+$/", $keyword)) {
			if (!empty($this->_joins)) {
				$quotedKeyword = "`" . $this->_table . "`.`" . $keyword . "`";
				return $quotedKeyword;
			}
			$quotedKeyword = "`" . $keyword . "`";
			return $quotedKeyword;
		}
		return $keyword;
	}

	private function _quoteValue($value) {
		return $this->_currentDb()->quote($value);
	}

	private function _parseError(\PDOStatement $stmt, $sql) {
		$this->_error = $stmt->errorInfo();
		if (in_array($this->_error[2], [ 2006, 2013 ])) {
			$this->_currentDb()->reconnect();
			$bool = $stmt->execute();
			if ($bool) {
				return;
			}
			$this->_error = $stmt->errorInfo();
		}

		if (is_cmd()) {
			throw new Exception($this->_error[2] . "\nSQL: {$sql}", $this->_error[1]);
		}
		else {
			throw new Exception($this->_error[2] . "\nSQL: <xmp>{$sql}</xmp>", $this->_error[1]);
		}
	}
}

?>