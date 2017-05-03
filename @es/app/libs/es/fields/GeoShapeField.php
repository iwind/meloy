<?php

namespace es\fields;

class GeoShapeField extends Field {

	public function type() {
		return self::TYPE_GEO_SHAPE;
	}
}

?>