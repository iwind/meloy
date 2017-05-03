<?php

namespace es\values;

class ShapeGeometryCollectionValue extends ShapeValue {
	/**
	 * @var Value[]
	 */
	private $_values = [];

	public function addValue(Value $value) {
		$this->_values[] = $value;
		return $this;
	}

	public function value() {
		$geometries = [];
		foreach ($this->_values as $value) {
			$geometries[] = $value->value();
		}
		return [
			"type" => "geometrycollection",
			"geometries" => $geometries
		];
	}
}

?>