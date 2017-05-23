<?php

namespace es\app\actions\doc;

use es\api\IndexDocApi;
use es\values\Value;

class JsonDataAction extends BaseAction {
	public function run(array $values, array $types) {
		$api = $this->_server->api(IndexDocApi::class); /** @var IndexDocApi $api */
		$api->index($this->_index)
			->type($this->_type);

		foreach ($values as $field => &$value) {
			$value = Value::formatWithType($value, $types[$field] ?? "string");
		}

		$this->data->values = $values;
	}
}

?>