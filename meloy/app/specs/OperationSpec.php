<?php

namespace app\specs;

/**
 * 对象操作相关规约
 */
class OperationSpec {
	private $_name;
	private $_code;

	public function __construct($name, $code) {
		$this->_name = $name;
		$this->_code = $code;
	}

	public function name() {
		return $this->_name;
	}

	public function code() {
		return $this->_code;
	}
}

?>