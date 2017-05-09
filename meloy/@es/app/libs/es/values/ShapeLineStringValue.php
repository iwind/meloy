<?php

namespace es\values;

class ShapeLineStringValue extends ShapeValue {
	private $_points = [];

	public function addPoint($x, $y) {
		$this->_points[] = [ $x, $y ];
		return $this;
	}

	public function points() {
		return $this->_points;
	}

	public function value() {
		return [
			"type" => "linestring",
			"coordinates" => $this->_points
		];
	}
}

?>