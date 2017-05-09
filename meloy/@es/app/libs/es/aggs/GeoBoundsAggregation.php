<?php

namespace es\aggs;

class GeoBoundsAggregation extends Aggregation {
	private $_field;
	private $_wrapLongitude;

	public function setField($field) {
		$this->_field = $field;

		return $this;
	}

	public function setWrapLongitude($wrapLongitude) {
		$this->_wrapLongitude = $wrapLongitude;
		return $this;
	}

	public function asArray() {
		$arr = [
			"field" => $this->_field
		];

		if (is_bool($this->_wrapLongitude)) {
			$arr["wrap_longitude"] = $this->_wrapLongitude;
		}

		return [
			"geo_bounds" => $arr
		];
	}
}

?>