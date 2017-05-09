<?php

namespace es\aggs;

/**
 * Class ExtendedStatsAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class ExtendedStatsAggregation extends Aggregation {
	private $_field;
	private $_sigma;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setSigma($sigma) {
		$this->_sigma = $sigma;
		return $this;
	}

	public function asArray() {
		$arr = [
			"field" => $this->_field
		];
		if (!is_null($this->_sigma)) {
			$arr["sigma"] = $this->_sigma;
		}
		return [
			"extended_stats" => $arr
		];
	}
}

?>