<?php

namespace es\app\actions\type;

use es\api\IndexDocApi;
use es\Exception;
use es\values\Value;
use tea\Must;

class AddDocAction extends BaseAction {
	public function run(string $id, array $values, array $types, Must $must) {
		$must->field("id", $id)
			->require("请输入文档数据的ID");

		$api = $this->_server->api(IndexDocApi::class); /** @var IndexDocApi $api */
		$api->index($this->_index)
			->type($this->_type);

		foreach ($values as $field => &$value) {
			$value = Value::formatWithType($value, $types[$field] ?? "string");
		}

		try {
			$api->put($id, $values);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->refresh()->success("保存成功");
	}
}

?>