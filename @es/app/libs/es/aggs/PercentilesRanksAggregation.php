<?php

namespace es\aggs;

/**
 * Class PercentilesRanksAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class PercentilesRanksAggregation extends Aggregation {
	private $_field;
	private $_values;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setValues($values) {
		$this->_values = $values;
		return $this;
	}

	public function asArray() {
		$arr = [
			"field" => $this->_field
		];
		if (!is_null($this->_values)) {
			$arr["values"] = $this->_values;
		}
		return [
			"percentile_ranks" => $arr
		];
	}
}

?>