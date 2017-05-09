<?php

namespace es\fields;

class DateField extends Field {

	public function type() {
		return "date";
	}

	public function setFormat($format) {
		$this->setParam("format", $format);
	}
}

?>