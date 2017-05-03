<?php

namespace es\fields;

class ByteField extends NumericField {

	public function type() {
		return self::TYPE_BYTE;
	}
}

?>