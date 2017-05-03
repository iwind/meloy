<?php

namespace es\aggs;

/**
 * Class SumAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class SumAggregation extends Aggregation {
	private $_field;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function asArray() {
		return [
			"sum" => [
				"field" => $this->_field
			]
		];
	}
}

?>