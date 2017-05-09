<?php

namespace tea;

abstract class Filter {
	private $_hasBefore = false;

	public abstract static function new();
	public abstract function before(&$object);
	public abstract function after(&$object);

	public function runBefore(&$object) {
		$this->_hasBefore = true;
		return $this->before($object);
	}

	public function runAfter(&$object) {
		if ($this->_hasBefore) {
			return $this->after($object);
		}
		return true;
	}
}

?>