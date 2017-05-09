<?php

namespace tea\auth;

class ShouldAuth {
	private $_ns;
	private $_failFn;

	public static function newForParam($param) {
		session_init();
		$auth = new static($param);
		return $auth;
	}

	public function __construct($ns) {
		$this->_ns = $ns . "@" . ip();
	}

	public function attrs() {
		if (isset($_SESSION) && is_array($_SESSION)) {
			$attrs = $_SESSION[$this->_ns] ?? [];
			if (is_array($attrs)) {
				return $attrs;
			}
		}
		return [];
	}

	public function attr($name, $default = "") {
		$attrs = $this->attrs();
		return $attrs[$name] ?? $default;
	}

	public function int($name, $min = null, $max = null) {
		$attr = intval($this->attr($name, 0));
		if (is_numeric($min) && $attr < $min) {
			$attr = $min;
		}
		if (is_numeric($max) && $attr > $max) {
			$attr = $max;
		}
		return $attr;
	}

	public function store($name, $value) {
		$_SESSION[$this->_ns][$name] = $value;
		return $this;
	}

	public function storeAttrs(array $attrs, $overwrite = false) {
		if ($overwrite) {
			$_SESSION[$this->_ns] = $attrs;
		}
		else {
			foreach ($attrs as $key => $value) {
				$_SESSION[$this->_ns][$key] = $value;
			}
		}
		return $this;
	}

	public function unset() {
		unset($_SESSION[$this->_ns]);
	}

	public function ifFail(callable $fn) {
		$this->_failFn = $fn;
		return $this;
	}

	/**
	 * 校验，供子类覆盖实现
	 *
	 * @return bool
	 */
	public function validate() {
		return !empty($this->attrs());
	}

	/**
	 * 失败时调用，供子类覆盖实现
	 */
	public function onFail() {
		if ($this->_failFn) {
			call_user_func($this->_failFn, $this);
		}
	}
}

?>