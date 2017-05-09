<?php

namespace tea;

class Exception extends \Exception {
	private $_cause;

	public function setCause($cause) {
		$this->_cause = $cause;
	}

	public function cause() {
		return $this->_cause;
	}
}

?>