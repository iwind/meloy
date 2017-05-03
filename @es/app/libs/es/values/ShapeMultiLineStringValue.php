<?php

namespace es\values;

class ShapeMultiLineStringValue extends ShapeValue {
	/**
	 * @var ShapeLineStringValue[]
	 */
	private $_lines = [];

	/**
	 * @return ShapeLineStringValue
	 */
	public function addLineString() {
		$line = new ShapeLineStringValue("");
		$this->_lines[] = $line;
		return $line;
	}

	public function value() {
		$points = [];
		foreach ($this->_lines as $line) {
			$points[] = $line->points();
		}
		return [
			"type" => "multilinestring",
			"coordinates" => $points
		];
	}
}



?>