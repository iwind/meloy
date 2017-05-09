<?php

namespace es\values;

class ShapeEnvelopeValue extends ShapeValue {
	private $_upperLeftPoint = [];
	private $_lowerRightPoint = [];

	public function setPoints($topLeft, $bottomRight) {
		$this->_upperLeftPoint = $topLeft;
		$this->_lowerRightPoint = $bottomRight;

		return $this;
	}

	public function value() {
		return [
			"type" => "envelope",
			"coordinates" => [ $this->_upperLeftPoint, $this->_lowerRightPoint ]
		];
	}
}

?>