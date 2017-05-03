<?php

namespace es\queries;

class TermQuery extends Query {
	private $_field;
	private $_value;

	public function name() {
		return "term";
	}

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function field() {
		return $this->_field;
	}

	public function setValue($value) {
		$this->_value = $value;
		return $this;
	}

	public function asArray() {
		return [
			$this->_field => $this->_value
		];
	}
}

?>