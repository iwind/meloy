<?php

namespace es\aggs;

class TermsAggregation extends Aggregation {
	private $_field;
	private $_size;
	private $_order = [];

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setSize($size) {
		$this->_size = $size;
		return $this;
	}

	public function asc($field) {
		$this->_order[$field] = "asc";
		return $this;
	}

	public function desc($field) {
		$this->_order[$field] = "desc";
		return $this;
	}

	public function asArray() {
		$arr = [
			"field" => $this->_field
		];
		if (!is_null($this->_size)) {
			$arr["size"] = $this->_size;
		}
		if (!empty($this->_order)) {
			$arr["order"] = $this->_order;
		}
		return [
			"terms" => $arr
		];
	}
}

?>