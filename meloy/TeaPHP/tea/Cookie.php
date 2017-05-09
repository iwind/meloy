<?php

namespace tea;

class Cookie {
	private $_name;

	public static function newForParam($param) {
		return new self($param);
	}

	public function __construct($name) {
		$this->_name = $name;
	}

	public function value($default = "") {
		return cookie($this->_name, $default);
	}
}

?>