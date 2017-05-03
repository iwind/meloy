<?php

namespace es\aggs;

class MatrixStatsAggregation extends Aggregation {
	private $_fields = [];

	public function addField(... $fields) {
		foreach ($fields as $field) {
			$this->_fields[] = $field;
		}
		return $this;
	}

	public function asArray() {
		$arr = [
			"fields" => $this->_fields
		];
		return [
			"matrix_stats" => $arr
		];
	}
}

?>