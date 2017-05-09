<?php

namespace es\aggs;

/**
 * Class CardinalityAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class CardinalityAggregation extends Aggregation {
	private $_field;

	public function setField($field) {
		$this->_field = $field;

		return $this;
	}

	public function asArray() {
		$arr = [
			"cardinality" => [
				"field" => $this->_field
			]
		];

		return $arr;
	}
}

?>