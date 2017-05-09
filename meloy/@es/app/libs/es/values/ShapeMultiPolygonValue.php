<?php

namespace es\values;

class ShapeMultiPolygonValue extends ShapeValue {
	/**
	 * @var ShapePolygonValue[]
	 */
	private $_polygons = [];

	public function addPolygon() {
		$polygon = new ShapePolygonValue("");
		$this->_polygons[] = $polygon;
		return $polygon;
	}

	public function value() {
		$points = [];
		foreach ($this->_polygons as $polygon) {
			$points[] = $polygon->points();
		}

		return [
			"type" => "multipolygon",
			"coordinates" => $points
		];
	}
}

?>