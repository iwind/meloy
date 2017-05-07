<?php

namespace es\app\actions\type;

use es\api\GetMappingApi;

class BuilderAction extends BaseAction {
	public function run() {
		$api = $this->_server->api(GetMappingApi::class);/** @var GetMappingApi $api */
		$api->index($this->_index);
		$api->type($this->_type);
		$this->data->fields = $api->get()->properties;
	}
}

?>