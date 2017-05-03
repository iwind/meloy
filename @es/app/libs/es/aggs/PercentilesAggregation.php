<?php

namespace es\aggs;

/**
 * Class PercentilesAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class PercentilesAggregation extends Aggregation {
	private $_field;
	private $_percents;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setPercents($percents) {
		$this->_percents = $percents;
		return $this;
	}

	public function asArray() {
		$arr = [
			"field" => $this->_field
		];
		if (!is_null($this->_percents)) {
			$arr["percents"] = $this->_percents;
		}
		return [
			"percentiles" => $arr
		];
	}
}

?>