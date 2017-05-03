<?php

namespace es\queries;

use es\values\ShapeValue;

class GeoShapeQuery extends Query {
	const RELATION_INTERSECTS = "INTERSECTS";
	const RELATION_DISJOINT = "DISJOINT";
	const RELATION_WITHIN = "WITHIN";
	const RELATION_CONTAINS = "CONTAINS";

	private $_shapes = [];

	public function addField($field, ShapeValue $shape, $relation = self::RELATION_INTERSECTS) {
		$this->_shapes[$field] = [
			"shape" => $shape->value(),
			"relation" => $relation
		];

		return $this;
	}

	public function name() {
		return "geo_shape";
	}

	public function asArray() {
		return empty($this->_shapes) ? (object)[] : $this->_shapes;
	}
}

?>