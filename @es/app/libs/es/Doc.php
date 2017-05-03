<?php

namespace es;

use es\values\Value;

class Doc extends \stdClass {
	/**
	 * 索引
	 *
	 * @var string
	 */
	public static $INDEX;

	/**
	 * 数据映射
	 *
	 * @var string
	 */
	public static $MAPPING;

	/**
	 * @var DocMeta
	 */
	private $_meta;

	public function __construct(array $attrs = []) {
		$this->_meta = new DocMeta();

		foreach ($attrs as $attr => $value) {
			$this->$attr = $value;
		}
	}

	public function setAttr($name, $value) {
		if ($value instanceof Value) {
			$this->$name = $value->value();
		}
		else {
			$this->$name = $value;
		}
		return $this;
	}

	/**
	 * 取得元数据
	 *
	 * @return DocMeta
	 */
	public function meta() {
		return $this->_meta;
	}

	public function save() {
		return self::query()->insert($this->asArray());
	}

	public static function index() {
		return get_class_vars(static::class)["INDEX"];
	}

	public static function mapping() {
		return get_class_vars(static::class)["MAPPING"];
	}

	/**
	 * @return Query
	 */
	public static function query() {
		return (new Query())->model(static::class);
	}

	/**
	 * 取得批量操作容器
	 *
	 * @return TypeBulk
	 */
	public static function bulk() {
		return new TypeBulk(static::index(), static::mapping());
	}

	public function asArray() {
		$attrs = [];
		foreach (get_object_vars($this) as $key => $value) {
			if (in_array($key, [ "_meta" ])) {
				continue;
			}
			if ($value instanceof Value) {
				$value = $value->value();
			}
			$attrs[$key] = $value;
		}

		if (empty($attrs)) {
			return (object)[];
		}
		return $attrs;
	}

	public function asJson() {
		return json_encode($this->asArray());
	}

	public function asPrettyJson() {
		return json_encode($this->asArray(), JSON_PRETTY_PRINT);
	}
}

?>