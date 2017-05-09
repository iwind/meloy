<?php

namespace es\values;

class GeoPointValue extends Value {
	private $_value;

	public function setCoordinate($lat, $lng) {
		$this->_value = [ $lng, $lat ];
		return $this;
	}

	public function value() {
		return $this->_value;
	}
}

?>