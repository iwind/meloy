<?php

namespace es\app\actions\type;

use es\api\PutMappingApi;
use es\Exception;
use es\fields\Field;
use es\Mapping;

class UpdateFieldsAction extends BaseAction {
	public function run(array $fieldTypes, array $fieldNames) {
		//@TODO 对$fieldType需要有更严格的校验
		$mapping = new Mapping($this->_type);
		foreach ($fieldTypes as $index => $fieldType) {
			if (!isset($fieldNames[$index])) {
				$this->fail("字段名不能为空");
			}

			$fieldName = trim($fieldNames[$index]);
			if (is_empty($fieldName)) {
				$this->fail("字段名不能为空");
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
		$api->type($this->_type);

		try {
			$api->put($mapping);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->refresh()->success("保存成功");
	}
}

?>