<?php

namespace tea;

class Must {
	private static $_instance;
	private $_shouldThrow = false;

	private $_field;
	private $_value;

	public static function new () {
		return self::shared();
	}

	public static function shared () {
		if (self::$_instance == null) {
			self::$_instance = new static;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->_shouldThrow = (get_called_class() == self::class);
	}

	public function field($field = nil, $value = nil) {
		if (is_nil($field)) {
			return $this->_field;
		}
		else {
			$this->_field = $field;
			$this->_value = $value;
			return $this;
		}
	}

	public function int(&$var, $min = null, $max = null) {
		$var = intval($var);

		if (is_int($min) && $var < $min) {
			$var = $min;
			$this->_throw();
		}

		if (is_int($max) && $var > $max) {
			$var = $max;
			$this->_throw();
		}

		return $this;
	}

	public function float(&$var, $min = null, $max = null) {
		$var = floatval($var);

		if (is_float($min) && $var < $min) {
			$var = $min;
			$this->_throw();
		}

		if (is_float($max) && $var > $max) {
			$var = $max;
			$this->_throw();
		}

		return $this;
	}

	public function double(&$var, $min = null, $max = null) {
		$var = doubleval($var);

		if (is_double($min) && $var < $min) {
			$var = $min;
			$this->_throw();
		}

		if (is_double($max) && $var > $max) {
			$var = $max;
			$this->_throw();
		}

		return $this;
	}

	public function bool(&$var) {
		$var = boolval($var);
		return $this;
	}

	public function string(&$var, $default = "") {
		$var = strval($var);
		if (strlen($var) == 0) {
			$var = $default;
		}
		return $this;
	}

	public function array(&$var) {
		if (!is_array($var)) {
			$var = [];
			$this->_throw();
		}
		return $this;
	}

	public function pieces(&$var) {
		$var = strval($var);
		if (is_empty($var)) {
			$var = [];
			return $this;
		}

		$var = preg_split("/\\s*,\\s*/", $var);

		return $this;
	}

	public function ids(&$var) {
		if (!is_array($var)) {
			$var = strval($var);
			if (is_empty($var)) {
				$var = [];
				return $this;
			}

			$var = preg_split("/\\s*,\\s*/", $var);
		}

		$var = array_values(array_unique(
			array_filter(array_map("intval", $var), function ($value) {
				return $value > 0;
			})
		));

		return $this;
	}

	public function minLength($min, $message) {
		if (mb_strlen($this->_value, "UTF-8") < $min) {
			$this->_throw($message);
		}
		return $this;
	}

	public function maxLength($max, $message) {
		if (mb_strlen($this->_value, "UTF-8") > $max) {
			$this->_throw($message);
		}
		return $this;
	}

	public function require($message) {
		if (is_empty($this->_value)) {
			$this->_throw($message);
		}
		return $this;
	}

	public function match($regexp, $message) {
		if (!preg_match($regexp, $this->_value)) {
			$this->_throw($message);
		}
		return $this;
	}

	public function equal($value, $message) {
		if ($value !== $this->_value) {
			$this->_throw($message);
		}
		return $this;
	}

	/**
	 * 检查是否符合Email规则
	 *
	 * @param string $message 提示信息
	 * @return $this
	 */
	public function email($message) {
		$regex = "/^[a-z0-9]+([\\._\\-\\+]*[a-z0-9]+)*@([a-z0-9]+[\\-a-z0-9]*[a-z0-9]+\\.)+[a-z0-9]+$/i";
		if (!preg_match($regex, $this->_value)) {
			$this->_throw($message);
		}
		return $this;
	}

	public function if(callable $fn, $message) {
		$return = call_user_func($fn, $this->_value);
		if (!$return) {
			$this->_throw($message);
		}
		return $this;
	}

	private function _throw($message = null) {
		if (is_null($message)) {
			$message = "found a wrong param value";
		}
		if ($this->_shouldThrow) {
			$e = new Exception($message);
			$e->setCause($this);
			throw $e;
		}
	}
}

?>