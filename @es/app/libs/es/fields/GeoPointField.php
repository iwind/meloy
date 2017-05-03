<?php

namespace es\fields;

class GeoPointField extends Field {

	public function type() {
		return self::TYPE_GEO_POINT;
	}
}

?>