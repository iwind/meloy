<?php

namespace es\app\actions\indice;

use es\api\GetIndexApi;

class AliasesAction extends BaseAction {
	public function run() {
		$api = $this->_server->api(GetIndexApi::class);/** @var GetIndexApi $api */
		$api->index($this->_index);
		$info = $api->get();

		$this->data->count = count(object_keys($info->aliases));
		$this->data->aliases = $info->aliases;
	}
}

?>