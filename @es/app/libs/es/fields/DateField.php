<?php

namespace es\fields;

class DateField extends Field {

	public function type() {
		return self::TYPE_DATE;
	}

	public function setFormat($format) {
		$this->setParam("format", $format);
	}
}

?>