<?php

namespace es\app\actions\indice;

use es\api\IndexAliasesApi;
use es\Exception;
use tea\Must;

class AddAliasAction extends BaseAction {
	public function run(string $aliasName, string $routing, string $searchRouting, string $indexRouting, string $filter, Must $must) {
		//校验名称有效性
		$must->field("aliasName", $aliasName)
			->require("请输入别名名称")
			->match("/^[a-z0-9_]+$/", "名称只能为小写的字母、数字、下划线的组合")
			->match("/^[^_]/", "名称不能以下划线开头");

		//设置
		$api = $this->_server->api(IndexAliasesApi::class); /** @var IndexAliasesApi $api  */
		$api->index($this->_index);

		try {
			$api->add($aliasName, $routing, $searchRouting, $indexRouting, json_decode($filter));
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->refresh()->success("添加成功");
	}
}

?>