<?php

namespace es\app\actions\type;

use es\api\DeleteDocApi;
use tea\Must;

class DeleteDocAction extends BaseAction {
	public function run(string $id, Must $must) {
		$must->field("id", $id)
			->require("请指定要删除的文档ID");

		$api = $this->_server->api(DeleteDocApi::class);
		/** @var DeleteDocApi $api */
		$api->index($this->_index);
		$api->type($this->_type);
		$api->delete($id);

		$this->refresh()->success();
	}
}

?>