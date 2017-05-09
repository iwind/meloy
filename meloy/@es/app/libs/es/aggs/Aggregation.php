<?php

namespace es\aggs;

abstract class Aggregation {
	private $_name;
	private $_aggs = [];

	public function __construct($name) {
		$this->_name = $name;
	}

	public function name() {
		return $this->_name;
	}

	public function addAgg(self $agg) {
		$this->_aggs[] = $agg;
		return $this;
	}

	public function aggs() {
		return $this->_aggs;
	}

	public function asNestedArray() {
		$arr = $this->asArray();
		if (!empty($this->_aggs)) {
			foreach ($this->_aggs as $agg) {
				$arr["aggs"][$agg->name()] = $agg->asNestedArray();
			}
		}
		return $arr;
	}

	public abstract function asArray();
}

?>