<?php

namespace es\app\specs;

use es\api\GetMappingApi;
use tea\Tea;

class DbSpec extends \app\specs\DbSpec {
	protected $_tableTypeName = "类型";

	public function tables() {
		//加载ES操作库
		import(Tea::shared()->root() . DS . "@es/app/libs");

		/**
		 * @var GetMappingApi $api
		 */
		$api = $this->_server->api(GetMappingApi::class);
		$api->index($this->_name);

		$tables = [];
		foreach (object_keys($api->getAll()) as $typeName) {
			$tableSpec = new TableSpec();
			$tableSpec->name($typeName);
			$tables[] = $tableSpec;
		}
		return $tables;
	}

	public function operations() {
		return [];
	}
}

?>