<?php

namespace es\values;

class ShapePolygonValue extends ShapeValue {
	private $_outerPoints = [];
	private $_innerPoints = [];
	private $_orientation;

	public function addOuterPoint($x, $y) {
		$this->_outerPoints[] = [ $x, $y ];
	}

	public function addInnerPoint($x, $y) {
		$this->_innerPoints[] = [ $x, $y ];
	}

	public function setOrientation($orientation) {
		$this->_orientation = $orientation;
	}

	public function points() {
		if (empty($this->_innerPoints)) {
			return [ $this->_outerPoints ];
		}
		return [ $this->_outerPoints, $this->_innerPoints ];
	}

	public function value() {
		$value = [
			"type" => "polygon",
			"coordinates" => $this->points()
		];

		if (!is_empty($this->_orientation)) {
			$value["orientation"] = $this->_orientation;
		}

		return $value;
	}
}

?>