<?php

namespace es\queries;

class GeoDistanceQuery extends Query{
	private $_field;
	private $_distance;
	private $_coordinate;

	public function name() {
		return "geo_distance";
	}

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setDistance($distance) {
		$this->_distance = $distance;
		return $this;
	}

	public function setCoordinate($x, $y) {
		$this->_coordinate = [ $x, $y ];
		return $this;
	}

	public function asArray() {
		return [
			"distance" => $this->_distance,
			$this->_field => $this->_coordinate
		];
	}
}

?>