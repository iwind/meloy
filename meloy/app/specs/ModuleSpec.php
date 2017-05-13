<?php

namespace app\specs;

abstract class ModuleSpec {
	private $_name;
	private $_menuName;

	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}
		$this->_name = $name;
		return $this;
	}

	public function menuName($name = nil) {
		if (is_nil($name)) {
			return $this->_menuName;
		}
		$this->_menuName = $name;
		return $this;
	}
}

?>