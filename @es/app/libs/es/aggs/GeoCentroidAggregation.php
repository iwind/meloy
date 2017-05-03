<?php

namespace es\aggs;

class GeoCentroidAggregation extends Aggregation {
	private $_field;

	public function setField($field) {
		$this->_field = $field;

		return $this;
	}

	public function asArray() {
		$arr = [
			"field" => $this->_field
		];
		return [
			"geo_centroid" => $arr
		];
	}
}

?>