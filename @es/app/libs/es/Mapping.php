<?php

namespace es;

use es\fields\Field;

class Mapping {
	private $_name;

	/**
	 * @var Field[]
	 */
	private $_fields = [];

	public function __construct($name) {
		$this->_name = $name;
	}

	public function name() {
		return $this->_name;
	}

	public function add(Field $field) {
		$this->_fields[] = $field;
		return $this;
	}

	public function asArray() {
		$properties = [];
		foreach ($this->_fields as $field) {
			$properties[$field->name()] = $field->asArray();
		}
		if (!empty($properties)) {
			return [
				"properties" => $properties
			];
		}
		return [
			"properties" =>  (object)[]
		];
	}

	public function asJson() {
		return json_encode($this->asArray());
	}

	public function asPrettyJson() {
		return json_encode($this->asArray(), JSON_PRETTY_PRINT);
	}
}

?>