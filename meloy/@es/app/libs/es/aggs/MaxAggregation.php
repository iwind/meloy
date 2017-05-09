<?php

namespace es\aggs;

use es\scripts\Script;

/**
 * Class MaxAggregation
 *
 * @package es\aggs
 */
class MaxAggregation extends Aggregation {
	private $_field;

	/**
	 * @var Script
	 */
	private $_script;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setScript(Script $script) {
		$this->_script = $script;
		return $this;
	}

	public function asArray() {
		$arr = [];
		if (!is_null($this->_field)) {
			$arr = [
				"field" => $this->_field
			];
		}
		if (!is_null($this->_script)) {
			$arr = [
				"script" => $this->_script->asArray()
			];
		}
		return [
			"max" => $arr
		];
	}
}

?>