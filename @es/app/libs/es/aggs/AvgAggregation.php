<?php

namespace es\aggs;

/**
 * Class AvgAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class AvgAggregation extends Aggregation {
	private $_field;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function asArray() {
		return [
			"avg" => [
				"field" => $this->_field
			]
		];
	}
}

?>