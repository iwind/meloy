<?php

namespace es\app\actions\server;

use es\api\CreateIndexApi;
use es\Exception;
use tea\Must;

class CreateIndexAction extends BaseAction {
	public function run(string $name, Must $must) {
		//校验名称
		$must->field("name", $name)
			->require("请输入索引名称");

		//@TODO 校验名称中字符规则

		//发送接口
		try {
			/**
			 * @var CreateIndexApi $api
			 */
			$api = $this->_server->api(CreateIndexApi::class);
			$api->index($name)->create();
			$this->next("@.indice", [
				"serverId" => $this->_server->id(),
				"index" => $name
			])->success("创建成功");
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
}

?>