<?php

namespace es\app\actions\indice;

use es\api\PutMappingApi;
use es\Exception;
use es\fields\Field;
use es\Mapping;
use tea\Must;

class CreateTypeAction extends BaseAction {
	public function run(string $name, array $fieldTypes, array $fieldNames, Must $must) {
		//校验输入的参数
		$must->field("name", $name)
			->require("请输入类型名称");

		//@TODO 需要校验名称的有效性

		//@TODO 对$fieldType需要有更严格的校验
		$mapping = new Mapping($name);
		foreach ($fieldTypes as $index => $fieldType) {
			if (!isset($fieldNames[$index])) {
				$this->field("fieldNames[{$index}]", "字段名不能为空")->fail();
			}

			$fieldName = trim($fieldNames[$index]);
			if (is_empty($fieldName)) {
				$this->field("fieldNames[{$index}]", "字段名不能为空")->fail();
			}

			//@TODO 需要对$fieldNames[$index]进行更严格的校验

			$field = Field::fieldWithType($fieldType);
			$field->setName($fieldName);
			$mapping->add($field);
		}

		if ($mapping->countFields() == 0) {
			$this->fail("请添加字段");
		}

		/**
		 * @var PutMappingApi $api
		 */
		$api = $this->_server->api(PutMappingApi::class);
		$api->index($this->_index);
		$api->type($name);

		try {
			$api->put($mapping);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->next("@.type", [
			"serverId" => $this->_server->id,
			"index" => $this->_index,
			"type" => $name
		])->success("添加成功");
	}
}

?>