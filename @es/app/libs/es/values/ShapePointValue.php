<?php

namespace es\values;

class ShapePointValue extends ShapeValue {
	private $_coordinate = [];

	public function setCoordinate($x, $y) {
		$this->_coordinate = [ $x, $y ];
		return $this;
	}

	public function value() {
		return [
			"type" => "point",
			"coordinates" => $this->_coordinate
		];
	}
}

?>