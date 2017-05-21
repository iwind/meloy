<?php

namespace es\app\actions\indice;

use es\api\IndexAliasesApi;
use tea\Must;

class DeleteAliasAction extends BaseAction {
	public function run(string $alias, Must $must) {
		$must->field("alias", $alias)
			->require("请输入要删除的别名");

		$api = $this->_server->api(IndexAliasesApi::class); /** @var IndexAliasesApi $api  */
		$api->index($this->_index);
		$api->remove($alias);

		$this->refresh()->success("删除成功");
	}
}

?>