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
	 * @param $value;
	 * @return static
	 */
	public static function create($value = null) {
		return new static($value);
	}
}

?>