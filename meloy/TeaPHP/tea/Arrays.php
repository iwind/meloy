<?php

namespace tea;

class Arrays {
	/**
	 * 取得一个数组中中某个键的值
	 *
	 * @param array|object $array 数组
	 * @param array|string $keys 键，可以是多级的，比如a.b.c
	 * @param mixed $default 默认值
	 * @return mixed
	 * @see Arrays::set
	 */
	public static function get($array, $keys, $default = null) {
		if (is_array($keys) && empty($keys)) {
			return $array;
		}
		if (is_scalar($keys) && strlen($keys) == 0) {
			return $array;
		}
		$isObject = is_object($array);
		if ($array instanceof \ArrayAccess) {
			$isObject = false;
		}
		if (!is_array($keys)) {
			if (strstr($keys, "`")) {
				$keys = preg_replace("/`(.+)`/Ue", "str_replace('.','\\.','\\1')", $keys);
			}
			$keys = preg_split("/(?<!\\\\)\\./", $keys);
		}

		$firstKey = array_shift($keys);
		$value = null;
		if ($isObject) {
			$properties = get_object_vars($array);
			$value = array_key_exists($firstKey, $properties) ? $array->$firstKey :  $default;
		}
		else {
			$value = array_key_exists($firstKey, $array) ? $array[$firstKey] : $default;
		}

		if (empty($keys)) {
			return $value;
		}

		if (is_array($value) || is_object($value)) {
			return self::get($value, $keys, $default);
		}

		return null;
	}

	/**
	 * 设置一个数组中某个键的值，并返回设置后的值
	 *
	 * 对原有的数组没有影响
	 *
	 * @param array $array 数组
	 * @param array|string $keys 键，可以是多级的，比如a.b.c
	 * @param mixed $value 新的键值
	 * @return array
	 * @see Arrays::get
	 */
	public static function set(array $array, $keys, $value) {
		if (is_array($keys) && empty($keys)) {
			return $array;
		}
		if (!is_array($keys)) {
			/**if (strstr($keys, "`")) {
			 * $keys = preg_replace("/`(.+)`/Ue", "str_replace('.','\.','\\1')", $keys);
			 * }
			 * $keys = preg_split("/(?<!\\\)\./", $keys);**/
			$keys = explode(".", $keys);
		}
		if (count($keys) == 1) {
			$firstKey = array_pop($keys);
			$firstKey = str_replace("\\.", ".", $firstKey);
			$array[$firstKey] = $value;
			return $array;
		}
		$lastKey = array_pop($keys);
		$lastKey = str_replace("\\.", ".", $lastKey);
		$lastConfig = &$array;
		foreach ($keys as $key) {
			$key = str_replace("\\.", ".", $key);
			if (!isset($lastConfig[$key]) || !is_array($lastConfig[$key])) {
				$lastConfig[$key] = [];
			}
			$lastConfig = &$lastConfig[$key];
		}
		$lastConfig[$lastKey] = $value;
		return $array;
	}

	/**
	 * pick values from an array
	 *
	 * @param array $array input array
	 * @param string|integer $key key
	 * @param boolean $keepIndex if keep index
	 * @return array
	 * @since 1.0
	 */
	public static function pick($array, $key, $keepIndex = false) {
		if (!is_array($array)) {
			return [];
		}
		$ret = [];
		foreach ($array as $index => $row) {
			if (is_array($row) || is_object($row)) {
				$value = Arrays::get($row, $key);
				if ($keepIndex) {
					$ret[$index] = $value;
				} else {
					$ret[] = $value;
				}
			}
		}
		return $ret;
	}

	/**
	 * sort multiple-array by key
	 *
	 * @param array $array array to sort
	 * @param mixed $key string|array
	 * @param boolean $asc if asc
	 * @return array
	 */
	public static function sort(array $array, $key = null, $asc = true) {
		if (empty($array)) {
			return $array;
		}
		if (empty($key)) {
			$asc ? asort($array) : arsort($array);
		} else {
			$GLOBALS["TEA_ARRAY_SORT_KEY_" . nil] = $key;
			uasort($array,
				$asc ? create_function('$p1,$p2', '$key=$GLOBALS["TEA_ARRAY_SORT_KEY_" . nil];$p1=\tea\Arrays::get($p1,$key);$p2=\tea\Arrays::get($p2,$key);if ($p1>$p2){return 1;}elseif($p1==$p2){return 0;}else{return -1;}')
					:
					create_function('$p1,$p2', '$key=$GLOBALS["TEA_ARRAY_SORT_KEY_" . nil];$p1=\tea\Arrays::get($p1,$key);$p2=\tea\Arrays::get($p2,$key);if ($p1<$p2){return 1;}elseif($p1==$p2){return 0;}else{return -1;}')
			);
			unset($GLOBALS["TEA_ARRAY_SORT_KEY_" . nil]);
		}
		return $array;
	}

	/**
	 * 从一个数组的值中选取key做当前数组的key
	 *
	 * <code>
	 * $array = array(
	 *   array("a" => 11, "b" => 12),
	 *   array("a" => 21, "b" => 22)
	 *   //...
	 * );
	 *
	 * $array2 = Arrays::combine($array, "a", "b");
	 * </code>
	 *
	 * $array2就变成了：
	 * <code>
	 * array(
	 *   11 => 12,
	 *   21 => 22
	 * );
	 * </code>
	 *
	 * 如果$valueName没有值，则是把当前元素值当成value:
	 *
	 * <code>
	 * $array2 = Arrays::combine($array, "a");
	 *
	 * array(
	 *   11 => array("a" => 11, "b" => 12),
	 *   21 => array("a" => 21, "b" => 22)
	 * );
	 * </code>
	 *
	 * 支持以点(.)符号连接的多层次keyName和valueName：
	 * - Arrays::combine($array, "a.b", "a.c");
	 * 即重新构成了一个以$array[n][a][b]为键，以$array[n][a][c]为值的数组，其中n是数组的索引
	 *
	 * @param array $array 二维数组
	 * @param integer|string $keyName 选取的key名称
	 * @param integer|string $valueName 选取的值名称
	 * @return array
	 * @since 1.0
	 */
	public static function combine($array, $keyName, $valueName = null) {
		$ret = [];
		foreach ($array as $row) {
			if (is_array($row) || is_object($row)) {
				$keyValue = self::get($row, $keyName);
				$value = is_null($valueName) ? $row : self::get($row, $valueName);
				if ($keyValue) {
					$ret[$keyValue] = $value;
				} else {
					$ret[] = $value;
				}
			}
		}
		return $ret;
	}

	/**
	 * 判断一个值是否在一个数组，或一个以逗号连接的字符串，或对象的属性中存在
	 *
	 * - Arrays::in(1, array(1, 2)); // => true
	 * - Arrays::in(1, "1,2"); // => true
	 * - Arrays::in(1, $obj);
	 *
	 * @param mixed $value 值
	 * @param mixed $array 数组、字符串或对象
	 * @param boolean $strict 是否严格比较
	 * @return boolean
	 * @since 1.0
	 */
	public static function in($value, $array, $strict = false) {
		if (is_scalar($array)) {
			$array = preg_split("/\\s*,\\s*/", $array);
		}
		else if (is_object($array)) {
			$array = get_object_vars($array);
		}
		return (is_array($array) && in_array($value, $array, $strict));
	}

	/**
	 * 切割使用逗号分隔的字符串
	 *
	 * @param string $string 字符串
	 * @param callable $converter 数据转换
	 * @return array
	 */
	public static function split($string, callable $converter = null) {
		if (is_null($string)) {
			return [];
		}
		$string = trim($string);
		if (strlen($string) == 0) {
			return [];
		}
		$pieces = preg_split("/\\s*,\\s*/", $string);
		$numbers = [];
		foreach ($pieces as $piece) {
			if ($converter) {
				$numbers[] = call_user_func($converter, $piece);
			}
			else {
				if (is_numeric($piece)) {
					$numbers[] = (int)$piece;
				}
			}
		}
		return $numbers;
	}

	/**
	 * 扁平化数组或对象
	 *
	 * @param array|object $array 要操作的数组
	 * @param string $prefix 键前缀
	 * @param array $results 要返回的结果
	 * @return array
	 */
	public static function flatten($array, $prefix = null, &$results = []) {
		foreach ($array as $key => $value) {
			if (is_array($value) || is_object($value)) {
				self::flatten($value, $key, $results);
			}
			else {
				if (!is_null($prefix)) {
					$key = $prefix . "." . $key;
				}
				$results[$key] = $value;
			}
		}
		return $results;
	}
}

?>