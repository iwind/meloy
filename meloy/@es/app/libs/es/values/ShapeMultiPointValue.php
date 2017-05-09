<?php

namespace es\values;

class ShapeMultiPointValue extends ShapeValue {
	private $_points = [];

	public function addPoint($x, $y) {
		$this->_points[] = [ $x, $y ];
	}

	public function value() {
		return [
			"type" => "multipoint",
			"coordinates" => $this->_points
		];
	}
}

?>