<?php

namespace es\aggs;

/**
 * Class StatsAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class StatsAggregation extends Aggregation {
	private $_field;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function asArray() {
		return [
			"stats" => [
				"field" => $this->_field
			]
		];
	}
}

?>