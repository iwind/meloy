<?php

namespace es\aggs;

/**
 * Class ValueCountAggregation
 *
 * @package es\aggs
 * @TODO 支持script
 */
class ValueCountAggregation extends Aggregation {
	private $_field;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function asArray() {
		return [
			"value_count" => [
				"field" => $this->_field
			]
		];
	}
}

?>