<?php

namespace es\fields;

class ObjectField extends  Field {


	public function type() {
		return self::TYPE_OBJECT;
	}

	public function asArray() {
		$properties = [];
		foreach ($this->children() as $child) {
			$properties[$child->name()] = $child->asArray();
		}
		if (!empty($properties)) {
			return [
				"properties" => $properties
			];
		}
		return (object)[];
	}
}

?>