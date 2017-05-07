<?php

namespace es\app\actions\type;

use es\api\GetMappingApi;

class FieldsAction extends BaseAction {
	public function run() {
		$this->data->serverVersion = $this->serverVersion();

		$api = $this->_server->api(GetMappingApi::class); /** @var GetMappingApi $api */
		$api->index($this->_index);
		$api->type($this->_type);
		$mapping = $api->get();
		$this->data->mapping = $mapping;
	}
}

?>