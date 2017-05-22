<?php

namespace es\values;

class Value {
	private $_value;

	public function __construct($value = null) {
		$this->_value = $value;
	}

	public function value() {
		return $this->_value;
	}

	public function asJson() {
		return json_encode($this->value());
	}

	public function asPrettyJson() {
		return json_encode($this->value(), JSON_PRETTY_PRINT);
	}

	/**
	 * 构造新的值
	 *
	 * @param mixed $value 初始值
	 * @return static
	 */
	public static function create($value = null) {
		return new static($value);
	}

	/**
	 * 根据类型格式化值
	 *
	 * @param mixed $value 值
	 * @param string $type 类型
	 * @return mixed
	 */
	public static function formatWithType($value, $type){
		switch ($type) {
			case "text":
			case "keyword":
			case "string":
				break;
			case "long":
				$value = intval($value, 10);
				break;
			case "integer":
				$value = intval($value, 10);
				break;
			case "short":
				$value = intval($value, 10);
				break;
			case "byte":
				$value = intval($value, 10);
				break;
			case "double":
				$value = doubleval($value);
				break;
			case "float":
				$value = floatval($value);
				break;
			case "boolean":
				$value = (bool)$value;
				break;
			case "date":
				if (is_empty($value)) {
					$value = null;
				}
				break;
			case "ip":
				if (is_empty($value)) {
					$value = null;
				}
				break;
			case "geo_point":
				if (is_empty($value)) {
					$value = null;
				}
				if (is_array($value)) {
					if (isset($value[0])) {
						$value[0] = doubleval($value[0]);
					}
					if (isset($value[1])) {
						$value[1] = doubleval($value[1]);
					}
					if (isset($value[0]) && isset($value[1])) {
						$value = [ $value[0], $value[1] ];
					}
					if (isset($value["lat"])) {
						$value["lat"] = doubleval($value["lat"]);
					}
					if (isset($value["lon"])) {
						$value["lon"] = doubleval($value["lon"]);
					}
				}
				break;
		}

		return $value;
	}
}

?>