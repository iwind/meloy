<?php

namespace es\queries;

class RangeQuery extends TermQuery {
	private $_range = [];

	public function gt($value) {
		$this->_range["gt"] = $value;
		return $this;
	}

	public function gte($value) {
		$this->_range["gte"] = $value;
		return $this;
	}

	public function lt($value) {
		$this->_range["lt"] = $value;
		return $this;
	}

	public function lte($value) {
		$this->_range["lte"] = $value;
		return $this;
	}

	public function between($min, $max) {
		$this->gte($min);
		$this->lte($max);
		return $this;
	}

	public function name() {
		return "range";
	}

	public function asArray() {
		return [
			$this->field() => $this->_range
		];
	}
}

?>