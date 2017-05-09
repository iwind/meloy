<?php

namespace tea;

class Session {
	private $_name;

	public static function newForParam($param) {
		return new self($param);
	}

	public function __construct($name) {
		$this->_name = $name;
	}

	public function value($default = "") {
		if (!isset($_SESSION) || !is_array($_SESSION)) {
			return $default;
		}
		return $_SESSION[$this->_name] ?? $default;
	}
}

?>