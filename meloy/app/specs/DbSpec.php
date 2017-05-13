<?php

namespace app\specs;

abstract class DbSpec {
	private $_state;
	private $_name;

	public function state($state = nil) {
		if (is_nil($state)) {
			return $this->_state;
		}

		$this->_state = $state;
		return $this;
	}

	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}

		$this->_name = $name;
		return $this;
	}

	public function tables() {
		return [];
	}

	public function operations() {
		return [];
	}
}

?>