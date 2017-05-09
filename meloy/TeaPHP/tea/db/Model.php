<?php

namespace tea\db;

/**
 * 模型类
 *
 * @package tea\db
 */
class Model extends \stdClass implements \ArrayAccess {
	/**
	 * 当前模型类对应的数据库ID
	 *
	 * @var string
	 */
	public static $DB;

	/**
	 * 当前模型类对应的数据表
	 *
	 * @var string
	 */
	public static $TABLE;

	/**
	 * 当前模型的版本
	 *
	 * 可以用来标识数据库表结构的变化
	 *
	 * @var string
	 */
	public static $VERSION = "1.0";

	/**
	 * 当前模型类对应数据表的主键
	 *
	 * @var string
	 */
	public static $PRIMARY_KEY = "id";

	/**
	 * 是否自动填充createdAt和updatedAt等时间属性
	 *
	 * @var bool
	 */
	public static $FILL_TIME = true;

	private $_exceptAttrs = [];

	/**
	 * 构造器
	 *
	 * @param array $attrs 属性列表
	 */
	public function __construct(array $attrs = []) {
		if (!empty($attrs)) {
			foreach ($attrs as $field => $value) {
				$this->$field = $value;
			}
		}
		$this->onInit();
	}

	/**
	 * 保存数据
	 *
	 * @param null|bool $create 如果是bool值，则能指定本方法是否新建数据
	 * @return mixed 当前数据对象的主键值
	 * @throws \Exception
	 */
	public function save($create = null) {
		$id = $this[static::$PRIMARY_KEY];
		$isNew = is_bool($create) ? $create : (is_null($id) || $id == 0);

		if ($isNew) {
			$this->onBeforeCreate();
		}
		else {
			$this->onBeforeUpdate();
		}
		$this->onBeforeCreateAndUpdate();

		$query = self::query()->notify(false);
		foreach (normalize($this, false) as $field => $value) {
			if (in_array($field, $this->_exceptAttrs)) {
				continue;
			}
			if ((is_null($value) || ($value === 0)) && static::$FILL_TIME) {
				if ($isNew && $field == "createdAt") {
					$value = time();
				}
				else if (!$isNew && $field == "updatedAt") {
					$value = time();
				}
			}
			if (is_null($value)) {
				continue;
			}
			if ($field == static::$PRIMARY_KEY) {
				if (!is_bool($create)) {
					continue;
				}
			}
			$query->set($field, $value);
		}
		if ($isNew) {
			$query->insert();

			//如果非手工指定主键值，则自动获取
			if (!isset($this[static::$PRIMARY_KEY]) || !is_scalar($this[static::$PRIMARY_KEY])) {
				$lastId = $query->lastId();
				$this[static::$PRIMARY_KEY] = $lastId;
			}

			$this->onAfterCreateAndUpdate();
			$this->onAfterCreate();
		}
		else {
			$query->attr(static::$PRIMARY_KEY, $this->id());
			$query->limit(1);

			$query->save();

			$this->onAfterCreateAndUpdate();
			$this->onAfterUpdate();
		}

		return $this[static::$PRIMARY_KEY];
	}

	/**
	 * 删除当前对象
	 *
	 * @throws \Exception
	 */
	public function delete() {
		$this->onBeforeDelete();

		self::query()
			->notify(false)
			->pk($this->id())
			->limit(1)
			->delete();

		$this->onAfterDelete();
	}

	/**
	 * 获取当前对象的主键值
	 *
	 * @return mixed
	 */
	public function id() {
		return $this[static::$PRIMARY_KEY];
	}

	/**
	 * 将当前对象转换为JSON格式
	 *
	 * @return string
	 */
	public function asJSON() {
		return json_encode(normalize($this));
	}

	/**
	 * 将当前对象转换为更好看的JSON格式
	 *
	 * @return string
	 */
	public function asPrettyJSON() {
		return json_encode(normalize($this), JSON_PRETTY_PRINT);
	}

	/**
	 * 获取当前对象的所有属性
	 *
	 * @return array
	 */
	public function attrs() {
		return normalize($this, false);
	}

	/**
	 * 获取当前对象对应的表格
	 *
	 * @return array|mixed
	 */
	public static function table() {
		$table = static::$TABLE;
		if (is_array($table)) {
			array_walk($table, function (&$_table) {
				if (strstr($_table, '%')) {
					$config = o(":db.default");
					foreach ($config as $key => $value) {
						if (is_scalar($value)) {
							$_table = str_replace('%{' . $key . '}', $value, $_table);
						}
					}
				}
			});
			return $table;
		}
		if (strstr($table, '%')) {
			$config = o(":db.default");
			foreach ($config as $key => $value) {
				if (is_scalar($value)) {
					$table = str_replace('%{' . $key . '}', $value, $table);
				}
			}
		}

		return $table;
	}

	/**
	 * 获取当前对象的数据库ID
	 *
	 * @return string
	 */
	public static function db() {
		return static::$DB;
	}

	public function onInit() {}

	public function onBeforeCreate() {}

	public function onAfterCreate() {}

	public function onBeforeCreateAndUpdate() {}

	public function onAfterCreateAndUpdate() {}

	public function onBeforeUpdate() {}

	public function onAfterUpdate() {}

	public function onBeforeDelete() {}

	public function onAfterDelete() {}

	public function offsetGet($index) {
		return $this->$index;
	}

	public function offsetSet($index, $value) {
		$this->$index = $value;
	}

	public function offsetUnset($index) {
		unset($this->$index);
	}

	public function offsetExists($index) {
		return isset($this->$index);
	}

	public function __get($prop) {
		if (method_exists($this, $prop)) {
			$this->$prop = $this->$prop();
			$this->_exceptAttrs[] = $prop;
			return $this->$prop;
		}
	}

	/**
	 * 构造新的查询
	 *
	 * @return Query
	 */
	public static function query() {
		$query = new Query();
		$query->db(static::db());
		$query->table(static::table());
		$query->model(static::class);
		$query->pkName(static::$PRIMARY_KEY);
		return $query;
	}

	/**
	 * 查找单个对象
	 *
	 * @param mixed $pk
	 * @param $result 结果集
	 * @return static
	 */
	public static function find($pk = null, $result = null) {
		if (is_null($pk)) {
			return self::query()->result($result)->find();
		}
		else if (is_scalar($pk)) {
			return self::query()->result($result)->find($pk);
		}
		else if (is_array($pk)) {
			return self::query()->result($result)->attrs($pk)->find();
		}
		return null;
	}

	/**
	 * 根据参数查找对象
	 *
	 * @param string $pkName 主键参数名
	 * @return static
	 * @throws \Exception
	 */
	public static function findX($pkName = "id") {
		$model = self::query()
				->findx($pkName);
		if (!$model) {
			throw new \Exception("Record not found");
		}
		return $model;
	}

	/**
	 * 判断记录是否存在
	 *
	 * @param mixed $pk 主键值
	 * @return bool
	 */
	public static function exist($pk = null) {
		return !empty(self::findOne($pk, static::$PRIMARY_KEY));
	}

	/**
	 * 查找一条数据
	 *
	 * @param scalar|null $pk 主键值
	 * @param string|array|null $result 返回的字段
	 * @return array|null
	 */
	public static function findOne($pk = null, $result = null) {
		if (is_null($pk)) {
			return self::query()->result($result)->findOne();
		}
		else if (is_scalar($pk)) {
			return self::query()->result($result)->findOne($pk);
		}
		else if (is_array($pk)) {
			return self::query()->result($result)->attrs($pk)->findOne();
		}
		return null;
	}

	public static function cache($key, $life = 0) {
		return new CacheModel(static::class, $key, $life);
	}
}

?>