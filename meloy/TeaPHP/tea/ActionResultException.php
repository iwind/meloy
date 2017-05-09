<?php

namespace tea;

class ActionResultException extends Exception {
	private $_data;

	public function setData($data) {
		$this->_data = $data;
	}

	public function data() {
		return $this->_data;
	}
}

?>