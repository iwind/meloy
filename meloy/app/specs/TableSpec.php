<?php

namespace app\specs;

class TableSpec {
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

	public function operations() {
		return [];
	}
}

?>