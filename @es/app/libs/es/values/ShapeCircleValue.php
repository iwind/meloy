<?php

namespace es\values;

class ShapeCircleValue extends ShapeValue {
	private $_coordinate = [];
	private $_radius;

	public function setCenter($x, $y) {
		$this->_coordinate = [ $x, $y ];
		return $this;
	}

	public function setRadius($radius) {
		$this->_radius = $radius;
		return $this;
	}

	public function value() {
		return [
			"type" => "circle",
			"coordinates" => $this->_coordinate,
			"radius" => $this->_radius
		];
	}
}

?>